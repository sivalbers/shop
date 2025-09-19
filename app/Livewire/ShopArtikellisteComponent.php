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
        Log::info('ShopArtikellisteComponent->mount()');
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


    public function render(){

        $artikelIds = collect($this->aPositions)->pluck('artikelnr')->toArray();

        $artikelMap = Artikel::whereIn('artikelnr', $artikelIds)
            ->get()
            ->keyBy('artikelnr');

        $artikels = $this->myArtikels;

        return view('livewire.shop.shopartikelliste',
            [
              'artikels' => $artikels,
              'artikelMap' => $artikelMap
            ]);
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

            $warengruppe = Warengruppe::where('wgnr', $wgnr)->first();
            if ($warengruppe) {
                $this->selectedWarengruppeBezeichung = $warengruppe->bezeichnung;
            }

            $this->aPositions = \App\Repositories\PositionRepository::loadByWarengruppe($wgnr);

            $this->anzGefunden = count($this->aPositions);

            $this->selectedWarengruppe = $wgnr;
        } else {

            $this->selectedWarengruppeBezeichung = '';
            $this->aPositions[] = [
                'uid' => md5('00' . now()),
                'id' => 0,
                'menge' => 0,
                'artikelnr' => '',
                'is_favorit' => false,
            ] ;

            $this->selectedWarengruppe = null;
            $this->anzGefunden = 0;
        }

        /*
            $endTime = microtime(true);
            Differenz in Millisekunden berechnen
            $duration = ($endTime - $startTime) * 1000;
            Zeit in die Log-Datei schreiben
            Log::info('Dauer der Funktion "ShopArtikellisteComponent.selectWarengruppe":', ['Dauer (ms)' => $duration]);
        */

    }



    #[On('showArtikelsuche')]
    public function showArtikelSuch($suchArtikelNr, $suchBezeichnung){

        if (empty($suchArtikelNr) && empty($suchBezeichnung)) {

            $this->aPositions = [];
            $this->anzGefunden = 0;
            return;
        }
        Log::info(['B3a showArtikelSuch' => $suchBezeichnung ]);

        $this->lastSuchArtikelNr = $suchArtikelNr;
        $this->lastSuchBezeichnung = $suchBezeichnung;
        $this->selectedTab = Tab::arSuche;

        $this->aPositions = \App\Repositories\PositionRepository::loadSuchArtikel($suchArtikelNr, $suchBezeichnung);
        $this->anzGefunden = count($this->aPositions);

    }

    #[On('showArtikelSchnellerfassung')]
    public function selectSchnellerfassung($artikelArray, $sortiment)
    {

        $this->selectedTab = Tab::arSchnellerfassung;

        $this->aPositions = \App\Repositories\PositionRepository::loadBySchnellerfassung($artikelArray, $sortiment);

        $this->anzGefunden = count($this->aPositions);
    }

    #[On('showFavoritMitID')]
    public function showFavoritMitID($favoritId){
        $this->favoritenActiveId = $favoritId;

        $this->selectedTab = Tab::arFavoriten;

        $this->aPositions = \App\Repositories\PositionRepository::loadByFavoritId($favoritId);
        $this->anzGefunden = count($this->aPositions);
    }


    public function showArtikel($artikelnr){
        $this->dispatch('showArtikel' , ['artikelnr' => $artikelnr ]);
    }


    public function InBasket(){
        Log::info('ShopArtikellisteComponent.InBasket');
        $bestellung = Bestellung::getBasket();
        if ($bestellung) {
            $bestellung->datum = now();
            $bestellung->save();


            foreach ($this->aPositions as $key => $pos) {
                if ($pos['menge'] >0) {

                    if ($pos['id'] == 0){
                        $artikel = Artikel::where('artikelnr', $pos['artikelnr'])->first();
                        BestellungPos::Create([
                            'bestellnr' => $bestellung->nr,
                            'artikelnr' => $pos['artikelnr'],
                            'menge'     => $pos['menge'],
                            'epreis'    => $artikel->vkpreis,
                            'steuer'    => $artikel->steuer,
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
