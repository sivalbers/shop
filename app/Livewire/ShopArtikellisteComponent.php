<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Artikel;
use App\Models\Bestellung;
use App\Models\Config;
use App\Models\ArtikelSortiment;
use App\Models\Favorit;
use App\Models\FavoritPos;


use Exception;
use Livewire\Attributes\On;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Log;
use App\Enums\Tab;
use App\Models\BestellungPos;
use App\Models\Warengruppe;

class ShopArtikellisteComponent extends Component
{

    use WithPagination;


    public $aPositions = [];

    public $myArtikels = [];

    public $selectedWarengruppe = null;
    public $selectedWarengruppeBezeichung = '';
    public $showForm ;



    public $selectedTab = Tab::arWG;

    public $listKurz = true;

    public $anzGefunden = 0;

    public $artikelnr;
    public $favoriten = [];
    public $favoritenIDs = [];
    public $favoritenActiveId = 0;

    public $isModified = false ;
    public $artikel = null ;

    private $lastWgNr;
    private $lastSuchArtikelNr;
    private $lastSuchBezeichnung;


    const CONFIG_LISTKURZ = 'listKurz';


    public function mount()
    {
        Log::info('ShopArtikellisteComponent.mount()');
        $this->listKurz =  Config::userString(self::CONFIG_LISTKURZ) === 'true';
        $this->myArtikels = collect();

        $this->favoriten = Favorit::cFavoriten();

        $this->updateSelection();
    }

    #[On('renderShopArtikellisteComponent')]
    public function updateSelection(){

        $tab = session()->get('activeTab');
        if ( $tab === 'warengruppen'){
            $this->selectedTab = Tab::arWG;
        }
        elseif ($tab === 'suche'){
            $this->selectedTab = Tab::arSuche;
        }
        elseif ($tab === 'favoriten'){
            $this->selectedTab = Tab::arFavoriten;
        }
        elseif ($tab === 'schnellerfassung'){
            $this->selectedTab = Tab::arSchnellerfassung;
        }



        switch ($this->selectedTab){
            case Tab::arWG:
                $this->lastWgNr = configGet('aktiveWarengruppe');
                $this->selectWarengruppe($this->lastWgNr);
                break;
            case Tab::arSuche:
                $this->lastSuchArtikelNr = session()->get('suchArtikelNr');
                $this->lastSuchBezeichnung = session()->get('suchBezeichnung');
                $this->showArtikelSuch($this->lastSuchArtikelNr, $this->lastSuchBezeichnung);
                break;
            case Tab::arFavoriten:
                $this->showFavoritMitID( configGet('aktiveFavorites'));
                break;
            case Tab::arSchnellerfassung:
                break;
        }
    }


    public function render()
    {
        Log::info('ShopArtikellisteComponent.render()');
        $artikels = $this->myArtikels;

        return view('livewire.shop.shopartikelliste', [ 'artikels' => $artikels ]);

    }


    public function checkArtikelType()
    {
        if ($this->myArtikels instanceof LengthAwarePaginator || $this->myArtikels instanceof Paginator || $this->myArtikels instanceof \Illuminate\Database\Eloquent\Collection ) {
            return true;
        } elseif ($this->myArtikels instanceof Collection) {
            return false;
        } else {
            return false;
        }
    }

    #[On('clearArtikelliste')]
    public function clearArtikelliste($tab){
        $this->aPositions = [];
        $this->anzGefunden = 0 ;
        $this->selectedTab = $tab;
    }


    #[On('showArtikelWG')]
    public function selectWarengruppe($wgnr){
        if (is_array($wgnr) && count($wgnr) > 0){
            $wgnr = $wgnr[0];
        }

        $sortiment = session()->get('sortiment');
        $startTime = microtime(true);

        session()->put('wgnr', $wgnr);

        $this->lastWgNr = $wgnr;

        $this->aPositions = array();

        if ($wgnr) {

            $this->selectedTab = Tab::arWG;


            $this->aPositions = array();

            $sortimentArray = explode(' ', $sortiment);

            $warengruppe = Warengruppe::where('wgnr', $wgnr)->first();
            if ($warengruppe) {
                $this->selectedWarengruppeBezeichung = $warengruppe->bezeichnung;
            }

            $kundennr = Session()->get('debitornr');
            $user_id = Auth::id();

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
                    AND a_s.sortiment IN ($inClause)
            ";

            $params = array_merge([$kundennr, $user_id, $wgnr], $sortimentArray);


            $this->myArtikels = DB::select($SQLquery, $params);


            foreach ($this->myArtikels as $artikel) {

                $this->aPositions[] = [
                    'uid' => md5($artikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'bezeichnung' => $artikel->bezeichnung,
                    'vkpreis' => $artikel->vkpreis,
                    'einheit' => $artikel->einheit,
                    'steuer' => $artikel->steuer,
                    'bestand' =>  $artikel->bestand,
                    'langtext' =>  $artikel->langtext,
                    'is_favorit' => $artikel->is_favorit,
                ] ;

            }
            $this->anzGefunden = count($this->aPositions);

            $this->selectedWarengruppe = $wgnr;
        } else {

            $this->anzGefunden = 0 ;
            $this->selectedWarengruppeBezeichung = '';
            $this->myArtikels = Artikel::join('artikel_sortimente as a_s', 'artikel.artikelnr', '=', 'a_s.artikelnr')
                ->where('artikel.wgnr', '')
                ->select('artikel.*')
                ->get();

            foreach ($this->myArtikels as $artikel) {

                $this->aPositions[] = [
                    'uid' => md5($artikel->artikelnr . now()),
                    'id' => 0,
                    'menge' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'bezeichnung' => $artikel->bezeichnung,
                    'vkpreis' => $artikel->vkpreis,
                    'einheit' => $artikel->einheit,
                    'steuer' => $artikel->steuer,
                    'bestand' =>  $artikel->bestand,
                    'langtext' =>  $artikel->langtext,
                ] ;
            }

            $this->selectedWarengruppe = null;

            $this->anzGefunden = 0;
        }

        $endTime = microtime(true);

        // Differenz in Millisekunden berechnen
        $duration = ($endTime - $startTime) * 1000;

        // Zeit in die Log-Datei schreiben
        // Log::info('Dauer der Funktion "ShopArtikellisteComponent.selectWarengruppe":', ['Dauer (ms)' => $duration]);

    }



    #[On('showArtikelsuche')]
    public function showArtikelSuch($suchArtikelNr, $suchBezeichnung){


        Log::info('showArtikelSuch angekommen');
        if (empty($suchArtikelNr) && empty($suchBezeichnung)) {

            $this->aPositions = [];
            $this->anzGefunden = 0;
            return;
        }


        session()->put('suchArtikelNr', $suchArtikelNr);
        session()->put('suchBezeichnung', $suchBezeichnung);
        $this->lastSuchArtikelNr = $suchArtikelNr;
        $this->lastSuchBezeichnung = $suchBezeichnung;
        $this->selectedTab = Tab::arSuche;

        $sortiment = explode(' ', session()->get('sortiment'));

        $artikelArr = [];
        $artikelBezArr = [];

        if ($suchArtikelNr != '') {
            $artikelArr = explode(' ', $suchArtikelNr);
        }

        if ($suchBezeichnung != '') {
            $artikelBezArr = explode(' ', $suchBezeichnung);
        }

        $kundennr = Session()->get('debitornr');
        $userId = Auth::id();


        $q = Artikel::select('artikel.*')
        ->selectRaw("CASE WHEN EXISTS (
            SELECT 1
            FROM favoriten_pos f_p
            JOIN favoriten f ON f.id = f_p.favoriten_id

            WHERE f_p.artikelnr = artikel.artikelnr
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
                          ->orWhere('langtext', 'like', "%{$part}%");
                    });
                }
            }
        })
        ->whereIn('artikelnr', ArtikelSortiment::whereIn('sortiment', $sortiment)->pluck('artikelnr'))
        ->take(200);

        // Ergebnis abrufen
        $this->myArtikels = $q->get();

        $this->aPositions = [];

        // Mengen-Array für jedes gefundene Artikel
        foreach ($this->myArtikels as $artikel) {

            $this->aPositions[] = [
                'uid' => md5($artikel->artikelnr . now()),
                'id' => 0,
                'menge' => 0,
                'artikelnr' => $artikel->artikelnr,
                'bezeichnung' => $artikel->bezeichnung,
                'vkpreis' => $artikel->vkpreis,
                'einheit' => $artikel->einheit,
                'steuer' => $artikel->steuer,
                'bestand' =>  $artikel->bestand,
                'langtext' =>  $artikel->langtext,
                'is_favorit' => $artikel->is_favorit,
            ] ;

        }

        $this->anzGefunden = count($this->aPositions);
    }


    function findMengeByArtikelnummer(&$artikelArray, $artikelnummer) {
        foreach ($artikelArray as &$artikel) {
            if ($artikel['artikelnummer'] === $artikelnummer) {
                $artikel['artikelnummer'] = 'x';
                return $artikel['menge'];
            }
        }

        // Falls die Artikelnummer nicht gefunden wird, kann null zurückgegeben werden
        return null;
    }

    #[On('showArtikelSchnellerfassung')]
    public function selectSchnellerfassung($artikelArray, $sortiment)
    {

        $this->selectedTab = Tab::arSchnellerfassung;

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

        $this->myArtikels = array();
        foreach ($artikelArray as $art){

            $xx = $artikellist->firstWhere('artikelnr', $art['artikelnummer']);
            if ($xx){
                $this->myArtikels[] = $xx;
            }

        }

        $this->aPositions = [];

        foreach ($this->myArtikels as $artikel){
            $this->aPositions[] = [
                'uid' => md5($artikel->artikelnr . now()),
                'id' => 0,
                'menge' => $this->findMengeByArtikelnummer($artikelArray, $artikel->artikelnr),
                'artikelnr' => $artikel->artikelnr,
                'bezeichnung' => $artikel->bezeichnung,
                'vkpreis' => $artikel->vkpreis,
                'einheit' => $artikel->einheit,
                'steuer' => $artikel->steuer,
                'bestand' =>  $artikel->bestand,
                'langtext' =>  $artikel->langtext,
                'is_favorit' => $artikel->is_favorit,
            ] ;

        }

        $this->anzGefunden = count($this->myArtikels);
    }

    #[On('showFavoritMitID')]
    public function showFavoritMitID($favoritId){
        $this->favoritenActiveId = $favoritId;

        $this->selectedTab = Tab::arFavoriten;


        $sortimentArray = explode(' ', session()->get('sortiment'));

        $qu = Artikel::query()
            ->join('favoriten_pos as p', 'p.artikelnr', '=', 'artikel.artikelnr')
            ->join('favoriten as f', 'f.id', '=', 'p.favoriten_id')
            ->join('artikel_sortimente as s', 's.artikelnr', '=', 'artikel.artikelnr')
            ->where('f.id', $favoritId)
            ->whereIn('s.sortiment', $sortimentArray)
            ->where('artikel.gesperrt', '=', false)
            ->select('p.id', 'artikel.*', \DB::raw('true as is_favorit'))
            ->orderBy('p.sort', 'asc');


        Log::info($qu->toRawSql());

        $artikellist = $qu->get();

        $this->aPositions = [];

        foreach ($artikellist as $artikel){

            $this->aPositions[] = [
                'uid' => md5($artikel->artikelnr . now()),
                'id' => $artikel->id,
                'menge' => 0,
                'artikelnr' => $artikel->artikelnr,
                'bezeichnung' => $artikel->bezeichnung,
                'vkpreis' => $artikel->vkpreis,
                'einheit' => $artikel->einheit,
                'steuer' => $artikel->steuer,
                'bestand' =>  $artikel->bestand,
                'langtext' =>  $artikel->langtext,
                'is_favorit' => $artikel->is_favorit,
            ] ;

        }
        $this->anzGefunden = count($this->aPositions);
    }


    public function showArtikel($artikelnr){
        $this->dispatch('showArtikel' , ['artikelnr' => $artikelnr ]);
    }

/*
    #[On('updateQuantityPos')]
    public function updateQuantityPos($artikelnr, $quantity)
    {
        try{
            if ($quantity >= 0) {
               // $this->quantities[$artikelnr]['menge'] = $quantity;

                //$this->dispatch('updateQuantity' , $artikelnr, $quantity);
            }
        } catch (\Exception $e) {
            return log::error ( 'Fehler in updateQuantityPos: ' , [ $e->getMessage()]);
        }
    }
*/


    public function InBasket(){
        Log::info('ShopArtikellisteComponent.InBasket');
        $bestellung = Bestellung::getBasket();
        if ($bestellung) {
            $bestellung->datum = now();
            $bestellung->save();


            foreach ($this->aPositions as $key => $pos) {
                if ($pos['menge'] >0) {

                    if ($pos['id'] == 0){
                        BestellungPos::Create([
                            'bestellnr' => $bestellung->nr,
                            'artikelnr' => $pos['artikelnr'],
                            'menge' => $pos['menge'],
                            'epreis' => $pos['vkpreis'],
                            'steuer' => $pos['steuer'],
                            'sort' => 0,
                        ]);

                        $this->aPositions[$key]['menge'] = 0;

                    }
                }

            }


            $this->dispatch('updateNavigation');
            $this->dispatch('basket-cleared');

        }
    }

    public function toggle_listKurz(){
        $this->listKurz = !$this->listKurz;

        Config::setUserString(self::CONFIG_LISTKURZ, $this->listKurz ? 'true' : 'false');

    }


    public function favoritArtikelForm($artikelnr){
        $this->dispatch('favoritArtikelForm', $artikelnr);
    }

    public function favoritArtikelDelete($id){
        $this->dispatch('favoritArtikelDelete', $id);
    }

}
