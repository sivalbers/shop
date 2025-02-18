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


    public $myArtikels = [];
    public $quantities;
    public $selectedWarengruppe = null;
    public $selectedWarengruppeBezeichung = '';
    public $showForm ;



    public $selectedTab = Tab::arWG;

    public $listKurz = true;

    public $anzGefunden = 0;

    public $artikelnr;
    public $favoriten = [];
    public $favoritenIDs = [];

    public $isModified = false ;
    public $artikel = null ;

    private $lastWgNr;
    private $lastSuchArtikelNr;
    private $lastSuchBezeichnung;


    const CONFIG_LISTKURZ = 'listKurz';


    public function mount($quantities)
    {
        $this->listKurz =  Config::userString(self::CONFIG_LISTKURZ) === 'true';
        $this->myArtikels = collect();

        Log::info('ShopArtikellisteComponent.mount()');
        $this->favoriten = Favorit::cFavoriten();

        $this->updateSelection();
    }

    #[On('renderShopArtikellisteComponent')]
    public function updateSelection(){

        $tab = session()->get('activeTab');
        if ( $tab === 'tab1'){
            $this->selectedTab = Tab::arWG;
        }
        elseif ($tab === 'tab2'){
            $this->selectedTab = Tab::arSuche;
        }
        elseif ($tab === 'tab3'){
            $this->selectedTab = Tab::arFavoriten;
        }
        elseif ($tab === 'tab4'){
            $this->selectedTab = Tab::arSchnellerfassung;
        }

        Log::info(['In ShopArtikellistComponent', 'selectedTab' => $this->selectedTab]);

        switch ($this->selectedTab){
            case Tab::arWG:
                $this->lastWgNr = session()->get('wgnr');
                Log::info('Tab::arWG', ['lastWgNr' => $this->lastWgNr ]);
                $this->selectWarengruppe($this->lastWgNr);
                break;
            case Tab::arSuche:
                $this->lastSuchArtikelNr = session()->get('suchArtikelNr');
                $this->lastSuchBezeichnung = session()->get('suchBezeichnung');
                $this->showArtikelSuch($this->lastSuchArtikelNr, $this->lastSuchBezeichnung);
                break;
            case Tab::arFavoriten:
                $this->showFavoritMitID(session()->get('aktiveFavorites'));
                break;
            case Tab::arSchnellerfassung:
                break;
        }
    }


    public function render()
    {

        $artikels = $this->myArtikels;
        //dd($this->quantities);
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

    #[On('showArtikelWG')]
    public function selectWarengruppe($wgnr)
    {
        if (is_array($wgnr) && count($wgnr) > 0){
            $wgnr = $wgnr[0];
        }

        $sortiment = session()->get('sortiment');
        Log::info('selectWarengruppe', [ 'wgnr' => $wgnr, 'sortiment' => $sortiment ]);
        $startTime = microtime(true);
        session()->put('wgnr', $wgnr);

        $this->lastWgNr = $wgnr;

        if ($wgnr) {

            $this->selectedTab = Tab::arWG;
            $this->quantities = array();

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
            //Log::info('Params: ', $params);
            //AND (f.user_id = 0 or f.user_id = ?)
            // dd($params);
            //Log::info("SQL Query: " . $SQLquery);
            //Log::info("Parameters: ", $params);
            $this->myArtikels = DB::select($SQLquery, $params);

            // Initialisiere das quantities-Array mit Standardwerten (z.B. 0)
            foreach ($this->myArtikels as $artikel) {

                $this->quantities[(string)$artikel->artikelnr] = [
                    'menge' => 0,
                    'id' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'epreis' => $artikel->vkpreis,
                    'steuer' => $artikel->steuer,
                    'bestand' =>  $artikel->bestand,
                ];

            }
            $this->anzGefunden = count($this->quantities);

            $this->selectedWarengruppe = $wgnr;
        } else {
            $this->anzGefunden = 0 ;
            $this->selectedWarengruppeBezeichung = '';
            $this->myArtikels = Artikel::join('artikel_sortimente as a_s', 'artikel.artikelnr', '=', 'a_s.artikelnr')
                ->where('artikel.wgnr', '')
                ->select('artikel.*')
                ->get();

            foreach ($this->myArtikels as $artikel) {
                $this->quantities[(string)$artikel->artikelnr] = [
                    'menge' => 0,
                    'id' => 0,
                    'artikelnr' => $artikel->artikelnr,
                    'epreis' => $artikel->vkpreis,
                    'steuer' => $artikel->steuer,
                    'bestand' =>  $artikel->bestand
                ];

            }

            $this->selectedWarengruppe = null;

            $this->anzGefunden = 0;
        }

        $endTime = microtime(true);

        // Differenz in Millisekunden berechnen
        $duration = ($endTime - $startTime) * 1000;

        // Zeit in die Log-Datei schreiben
        Log::info('Dauer der Funktion "ShopArtikellisteComponent.selectWarengruppe":', ['Dauer (ms)' => $duration]);

    }



    #[On('showArtikelsuche')]

    public function showArtikelSuch($suchArtikelNr, $suchBezeichnung)
    {
        Log::info('showArtikelSuch', [$suchArtikelNr, $suchBezeichnung]);

        session()->put('suchArtikelNr', $suchArtikelNr);
        session()->put('suchBezeichnung', $suchBezeichnung);
        $this->lastSuchArtikelNr = $suchArtikelNr;
        $this->lastSuchBezeichnung = $suchBezeichnung;
        $this->selectedTab = Tab::arSuche;

        $sortiment = explode(' ', Auth::user()->sortiment);

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

        $this->quantities = [];

        // Mengen-Array für jedes gefundene Artikel
        foreach ($this->myArtikels as $art) {
            $this->quantities[(string)$art->artikelnr] = [
                'menge' => 0,
                'id' => 0,
                'artikelnr' => $art->artikelnr,
                'epreis' => $art->vkpreis,
                'steuer' => $art->steuer,
                'bestand' => $art->bestand,
            ];
        }

        $this->anzGefunden = count($this->quantities);
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

        $this->quantities = array();

        $artikelStr = '';

        foreach ($artikelArray as $art){
            $artikelStr = $artikelStr . $art['artikelnummer']. ', ';
        }

        // dd($artikelStr);
        //Log::info([ 'Artikelliste' => $artikelStr ]);
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

        //Log::info($qu->toRawSql());
        $artikellist = $qu->get();

        $this->myArtikels = array();
        foreach ($artikelArray as $art){

            $xx = $artikellist->firstWhere('artikelnr', $art['artikelnummer']);
            if ($xx){
                $this->myArtikels[] = $xx;
            }

        }
        //dd($this->myArtikels);

        $this->quantities = array();

        foreach ($this->myArtikels as $art){
            //Log::info(' ', []);
            //Log::info('Suche Artikel: ', [$art->artikelnr]);
            //Log::info('Vorher ', $artikelArray);
            $this->quantities[(string)$art->artikelnr] = [
                'menge' => $this->findMengeByArtikelnummer($artikelArray, $art->artikelnr),
                'id' => 0,
                'artikelnr' => $art->artikelnr,
                'epreis' => $art->vkpreis,
                'steuer' => $art->steuer,
                'bestand' =>  $art->bestand,
            ];
            //Log::info('Nachher', $artikelArray);

        }
        //dd($this->quantities);
        $this->anzGefunden = count($this->quantities);
    }

    #[On('showFavoritMitID')]
    public function showFavoritMitID($favoritId)
    {

        Log::info('Angekommen: showFavoritMitID', [$favoritId]);
        $this->selectedTab = Tab::arFavoriten;
        $this->quantities = array();

        $sortimentArray = explode(' ', Auth::user()->sortiment);

        $qu = Artikel::query()
            ->join('favoriten_pos as p', 'p.artikelnr', '=', 'artikel.artikelnr')
            ->join('favoriten as f', 'f.id', '=', 'p.favoriten_id')
            ->join('artikel_sortimente as s', 's.artikelnr', '=', 'artikel.artikelnr')
            ->where('f.id', $favoritId)
            ->whereIn('s.sortiment', $sortimentArray)
            ->where('artikel.gesperrt', '=', false)
            ->select('artikel.*', \DB::raw('true as is_favorit'));
        // Log::info( 'SQL-Auswhahl der Favoriten: ',[ $qu->toRawSql() ]);


        $artikellist = $qu->get();

        $this->myArtikels = array();

        $this->quantities = array();

        foreach ($artikellist as $art){
            $this->myArtikels[] = $art;

            $this->quantities[(string)$art->artikelnr] = [
                'menge' => $art->bestand,
                'id' => 0,
                'artikelnr' => $art->artikelnr,
                'epreis' => $art->vkpreis,
                'steuer' => $art->steuer,
                'bestand' =>  $art->bestand,
            ];


        }
        $this->anzGefunden = count($this->quantities);
    }


    public function showArtikel($artikelnr){
        $this->dispatch('showArtikel' , ['artikelnr' => $artikelnr ]);
    }


    #[On('updateQuantityPos')]
    public function updateQuantityPos($artikelnr, $quantity)
    {
        Log::info('ShopArtikellisteComponent=>updateQuantityPos', [ $artikelnr, $quantity ]);
        try{
            if ($quantity >= 0) {
                $this->quantities[$artikelnr]['menge'] = $quantity;
                Log::info('Neue Menge ', [ $this->quantities[$artikelnr] ]);
                //$this->dispatch('updateQuantity' , $artikelnr, $quantity);
            }
        } catch (\Exception $e) {
            return log::error ( 'Fehler in updateQuantityPos: ' , [ $e->getMessage()]);
        }
    }

    public function InBasket(){
        Log::info('ShopArtikellisteComponent=>inBasket()');

        $bestellung = Bestellung::getBasket();
        if ($bestellung) {
            $bestellung->datum = now();
            $bestellung->save();

            // Jetzt kannst du direkt auf die Mengen zugreifen
            $quantities = $this->quantities;
            // dd($quantities); // Zum Debuggen

            foreach ($quantities as $artikelnr => $data) {
                if ($data['menge'] >0) {
                    Log::info('in Basket ',[ 'menge' => $data['menge'], 'epreis' => $data['epreis'], 'gpreis' => $data['menge'] * $data['epreis']]);
                    if ($data['id'] == 0){
                        $pos = BestellungPos::Create([
                            'bestellnr' => $bestellung->nr,
                            'artikelnr' => $data['artikelnr'],
                            'menge' => $data['menge'],
                            'epreis' => $data['epreis'],
                            //'gpreis' => $data['menge'] * $data['epreis'],
                            'steuer' => $data['steuer'],
                            'sort' => 0,
                        ]);

                        Log::info('Before Bestellnr, artikelnr, menge, gpreis', [ $bestellung->nr, $data['artikelnr'], $quantities[$artikelnr]['menge'], $data['epreis'], $data['steuer'] ]);

                        $quantities[$artikelnr]['menge'] = 0;
                        $this->quantities[$artikelnr]['menge'] = 0;

                        Log::info('After Bestellnr, artikelnr, menge, gpreis', [ $bestellung->nr, $data['artikelnr'], $quantities[$artikelnr]['menge'], $data['epreis'], $data['steuer'] ]);
                    }
                }

            }

            //Log::info($this->quantities);


            $this->dispatch('updateNavigation');
            $this->dispatch('basket-cleared');

        }
    }

    public function toggle_listKurz(){
        $this->listKurz = !$this->listKurz;

        Config::setUserString(self::CONFIG_LISTKURZ, $this->listKurz ? 'true' : 'false');

    }


    public function showFavoritPosForm($artikelnr){
        $this->dispatch('showFavoritPosForm', $artikelnr);
    }

}
