<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Debitor;
use App\Models\UserDebitor;
use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\ArtikelSortiment;


use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionRepository
{


        public static function loadArtikelKomplett($artikelnr){
            $artikel  = Artikel::with('ersatzArtikel')->with('zubehoerArtikel')->where('artikelnr', $artikelnr)->first();

            $aPositions = [];



            $aPositions[] = [
                'uid' => md5($artikel->artikelnr . now()),
                'id' => 0,
                'menge' => 0,
                'artikelnr' => $artikel->artikelnr,
            ];

            foreach($artikel->zubehoerArtikel as $zubehoerArtikel){

                $aPositions[] = [
                    'uid' => md5($zubehoerArtikel->zubehoerArtikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => 0,
                    'artikelnr' => $zubehoerArtikel->zubehoerartikelnr,
                    'is_favorit' => false,
                    'art' => 'Zubehörartikel',
                ] ;
            }


            foreach($artikel->ersatzArtikel as $ersatzartikel){
                $aPositions[] = [
                    'uid' => md5($ersatzartikel->ersatzArtikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => 0,
                    'artikelnr' => $ersatzartikel->ersatzartikelnr,
                    'is_favorit' => false,
                    'art' => 'Ersatzartikel',
                ] ;
            }

            return $aPositions;
        }


        public static function loadByWarengruppe($wgnr){

            $sortiment  = session()->get('sortiment');
            $kundennr   = session()->get('debitornr');
            $user_id    = Auth::id();

            $sortimentArray = explode(' ', $sortiment);
            $inClause = implode(',', array_fill(0, count($sortimentArray), '?'));

            $SQLquery = "
                SELECT
                    a.artikelnr,
                    a.bezeichnung,
                    a.vkpreis,
                    a.steuer,
                    a.bestand,
                    a.langtext,
                    a.einheit,
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM favoriten_pos f_p
                            JOIN favoriten f ON f.id = f_p.favoriten_id
                            WHERE f_p.artikelnr = a.artikelnr
                                AND f.kundennr = ?
                                AND (f.user_id = 0 or f.user_id = ?)

                        ) THEN 1
                        ELSE 0
                    END AS is_favorit
                FROM artikel a
                JOIN artikel_sortimente a_s ON a_s.artikelnr = a.artikelnr
                WHERE a.wgnr = ?
                    and a.gesperrt = false
                    AND a_s.sortiment IN ($inClause)
                    GROUP BY a.artikelnr,
                            a.bezeichnung,
                            a.vkpreis,
                            a.steuer,
                            a.bestand,
                            a.langtext,
                            a.einheit,
                            is_favorit
                    ";

            $params = array_merge([$kundennr, $user_id, $wgnr], $sortimentArray);

            $myArtikels = DB::select($SQLquery, $params);

            Log::info(['SQL', $SQLquery]);
            Log::info(['Kundennr', $kundennr, 'userId', $user_id, 'wgnr', $wgnr, 'sortiment', $sortimentArray]);

            $aPositions = array();

            foreach ($myArtikels as $artikel) {
                Log::info(['Artikel', $artikel->artikelnr]);

                $aPositions[] = [
                    'uid' => md5($artikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    // 'bezeichnung' => $artikel->bezeichnung,
                    // 'vkpreis' => $artikel->vkpreis,
                    // 'einheit' => $artikel->einheit,
                    // 'steuer' => $artikel->steuer,
                    // 'bestand' =>  $artikel->bestand,
                    // 'langtext' =>  $artikel->langtext,
                    'is_favorit' => $artikel->is_favorit,
                ] ;

            }

            return $aPositions;
        }


        public static function loadSuchArtikel($suchArtikelNr, $suchBezeichnung){
            if (empty($suchArtikelNr) && empty($suchBezeichnung)) {

                $aPositions = [];
                return $aPositions;
            }



            $sortiment = explode(' ', session()->get('sortiment'));

            $artikelArr = [];
            $artikelBezArr = [];

            if ($suchArtikelNr != '') {
                $artikelArr = explode(' ', $suchArtikelNr);
            }

            if ($suchBezeichnung != '') {
                $artikelBezArr = explode(' ', $suchBezeichnung);
            }

            $kundennr = session()->get('debitornr');
            $userId = Auth::id();


            $q = Artikel::select('artikel.*')
                ->selectRaw("CASE WHEN EXISTS (
                    SELECT 1
                    FROM favoriten_pos f_p
                    JOIN favoriten f ON f.id = f_p.favoriten_id

                    WHERE artikel.gesperrt = false
                    and f_p.artikelnr = artikel.artikelnr
                    AND f.kundennr = ?
                    AND (f.user_id = 0 OR f.user_id = ?)
                    ) THEN 1 ELSE 0 END AS is_favorit", [$kundennr, $userId])
                ->where(function ($query) use ($artikelArr, $artikelBezArr) {
                    // Bedingung: Artikelnummer kann einen der Begriffe enthalten
                    if (!empty($artikelArr)) {
                        $query->where(function ($q) use ($artikelArr) {
                            foreach ($artikelArr as $part) {
                                $q->orWhere('artikelnr', 'like', "%{$part}%");
                            }
                        });
                    }

                    // Bedingung: alle Teile der Suchbezeichnung müssen in Bezeichnung oder Langtext vorkommen
                    if (!empty($artikelBezArr)) {
                        foreach ($artikelBezArr as $part) {
                            $query->where(function ($q) use ($part) {
                                $q->where('bezeichnung', 'like', "%{$part}%")
                                ->orWhere('langtext', 'like', "%{$part}%")
                                ->orWhere('artikelnr', 'like', "%{$part}%");
                            });
                        }
                    }
                })
                ->whereIn('artikelnr', ArtikelSortiment::whereIn('sortiment', $sortiment)->pluck('artikelnr'))
                ->take(200);

            // Ergebnis abrufen
            $myArtikels = $q->get();

            $aPositions = [];

            // Mengen-Array für jedes gefundene Artikel
            foreach ($myArtikels as $artikel) {

                $aPositions[] = [
                    'uid' => md5($artikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'is_favorit' => $artikel->is_favorit,
                ] ;
            }

            return $aPositions;
        }

        private static function findMengeByArtikelnummer(&$artikelArray, $artikelnummer) {
            foreach ($artikelArray as &$artikel) {
                if ($artikel['artikelnummer'] === $artikelnummer) {
                    $artikel['artikelnummer'] = 'x';
                    return $artikel['menge'];
                }
            }

            // Falls die Artikelnummer nicht gefunden wird, kann null zurückgegeben werden
            return null;
        }

        public static function loadBySchnellerfassung($artikelArray, $sortiment){

            $artikelStr = '';

            foreach ($artikelArray as $art){
                $artikelStr = $artikelStr . $art['artikelnummer']. ', ';
            }

            $artikelnummern = array_column($artikelArray, 'artikelnummer');
            $sortimentArray = explode(' ', $sortiment);

            $kundennr = Session()->get('debitornr');
            $userId = Auth::id();

            $qu = Artikel::join('artikel_sortimente as a_s', 'artikel.artikelnr', '=', 'a_s.artikelnr')
                    ->whereIn('artikel.artikelnr', $artikelnummern)
                    ->whereIn('a_s.sortiment', $sortimentArray)
                    ->select('artikel.*')
                    ->selectRaw("CASE WHEN EXISTS (
                        SELECT 1
                        FROM favoriten_pos f_p
                        JOIN favoriten f ON f.id = f_p.favoriten_id

                        WHERE f_p.artikelnr = artikel.artikelnr
                        AND f.kundennr = ?
                        AND (f.user_id = 0 OR f.user_id = ?)
                        ) THEN 1 ELSE 0 END AS is_favorit", [$kundennr, $userId]);


            $artikellist = $qu->get();

            $myArtikels = array();
            foreach ($artikelArray as $art){

                $xx = $artikellist->firstWhere('artikelnr', $art['artikelnummer']);
                if ($xx){
                    $myArtikels[] = $xx;
                }

            }

            $aPositions = [];

            foreach ($myArtikels as $artikel){
                $aPositions[] = [
                    'uid' => md5($artikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => PositionRepository::findMengeByArtikelnummer($artikelArray, $artikel->artikelnr),
                    'artikelnr' => $artikel->artikelnr,
                    'is_favorit' => $artikel->is_favorit,
                ] ;

            }
            return $aPositions;
        }

        public static function loadByFavoritId($favoritId){
            $sortimentArray = explode(' ', session()->get('sortiment'));

            $qu = Artikel::query()
                ->join('favoriten_pos as p', 'p.artikelnr', '=', 'artikel.artikelnr')
                ->join('favoriten as f', 'f.id', '=', 'p.favoriten_id')
                ->join('artikel_sortimente as s', 's.artikelnr', '=', 'artikel.artikelnr')
                ->where('f.id', $favoritId)
                ->whereIn('s.sortiment', $sortimentArray)
                ->where('artikel.gesperrt', '=', false)
                ->select('p.id', 'artikel.*', DB::raw('true as is_favorit'))
                ->orderBy('p.sort', 'asc');


            Log::info($qu->toRawSql());

            $artikellist = $qu->get();

            $aPositions = [];

            foreach ($artikellist as $artikel){

                $aPositions[] = [
                    'uid' => md5($artikel->artikelnr . now()),
                    'id' => $artikel->id,
                    'menge' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    // 'bezeichnung' => $artikel->bezeichnung,
                    // 'vkpreis' => $artikel->vkpreis,
                    // 'einheit' => $artikel->einheit,
                    // 'steuer' => $artikel->steuer,
                    // 'bestand' =>  $artikel->bestand,
                    // 'langtext' =>  $artikel->langtext,
                    'is_favorit' => $artikel->is_favorit,
                ] ;

            }

            return $aPositions;
        }
}
