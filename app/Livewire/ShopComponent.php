<?php

namespace App\Livewire;


use Livewire\Component;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;




use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\Sortiment;
use App\Models\Bestellung;
use App\Models\Anschrift;
use App\Models\Position;
use App\Models\Favorit;
use App\Models\FavoritPos;
use PhpParser\Node\Stmt\Foreach_;

use function PHPUnit\Framework\isEmpty;

class ShopComponent extends Component
{

    /**
     * Get the view / contents that represent the component.
     */
    public $warengruppen;
    public $sortiment = 'EWE';

    public $aktiveWarengruppe;
    public $aktiveWarengruppeBezeichung = '';
    public $aktiveFavorites;
    public $showForm;
    public $showFavoritForm;
    public $zeigeFavoritPosForm = false;
    public $zeigeMessage = false ;

    public $artikelnr;
    public $isModified ;

    public $countBasket;

    public $mArtikel = null;

    public $quantities = null; // Array zum Speichern der Mengen für jeden Artikel

    public $activeTab = 'tab1';

    protected $queryString = ['activeTab' => ['except' => 'tab1']];

    public $suchArtikelnr = '';
    public $suchBezeichnung = '';
    public $expanded = false ;

    public $favoritId;
    public $favoritName;
    public $favoritUser;
    public $favoriten = [];

    public $favoritenIDs = [];

    public function mount()
    {
        debugLog(0, 'ShopComponent.mount()');
        $this->showFavoritForm = false;

        $this->zeigeFavoritPosForm = false;
        $this->suchArtikelnr = '';
        $this->suchBezeichnung = '';
        $this->sortiment = Auth::user()->sortiment;
        $tab = request()->query('tab', '');
        if ($tab != ''){
            session()->put('activeTab', $tab);
            $this->activeTab = $tab;
        }
        $this->updateQueryWG();
    }

    public function render()
    {
        Log::info('...');
        Log::info('ShopComponent.Render() - Anfang', ['Sortiment' => $this->sortiment, 'activeTab' =>$this->activeTab ]);

        if (($this->activeTab === 'tab1' && empty($this->warengruppen)) ||
            ($this->activeTab === 'tab3' && empty($this->favoriten))
           ){
            $this->updateQueryWG();
        }

        $startTime = microtime(true);
        $view = view('livewire.shop.shopmain');
        $endTime = microtime(true);

         $duration = ($endTime - $startTime) * 1000;

        Log::info("ShopComponent.render.view Ende " , [ 'duration' => $duration]);
        return $view;
    }


    public function updateQueryWG(){
        Log::info('ShopComponent.Update()', ['Sortiment' => $this->sortiment, 'activeTab' =>$this->activeTab ]);

        if ($this->activeTab === 'tab1') {
            $sortimentArray = explode ( ' ', $this->sortiment);

            if (is_null($this->quantities)){
                $this->quantities = array();
            }

            $query = Warengruppe::select('warengruppen.wgnr', 'warengruppen.bezeichnung', DB::raw('COUNT(artikel.artikelnr) AS artikel_count'))
            ->join('artikel', 'warengruppen.wgnr', '=', 'artikel.wgnr')
            ->join('artikel_sortimente', 'artikel.artikelnr', '=', 'artikel_sortimente.artikelnr')
            ->whereIn('artikel_sortimente.sortiment', $sortimentArray)
            ->groupBy('warengruppen.wgnr', 'warengruppen.bezeichnung')
            ->orderBy('warengruppen.bezeichnung');

            //Log::info('SQL ANWEISUNG:');
            //Log::warning($query->toRawSql());

            //$this->warengruppen = $query->get();

            $results = $query->get();

            $this->warengruppen = [];

            $results->each(function($item) use (&$wg) {
                $this->warengruppen[] = [
                    'wgnr' => $item->wgnr,
                    'bezeichnung' => $item->bezeichnung,
                    'artikel_count' => $item->artikel_count,
                ];
            });

        }
        elseif ($this->activeTab === 'tab3') {
            $this->favoriten = Favorit::cFavoriten();
        }
    }


    public function updatedSuchText($value){
    }

    public function updateSuche(){

        if ($this->suchArtikelnr || $this->suchBezeichnung){
            // Log::info('updateSuche',[ $this->suchArtikelnr, $this->suchBezeichnung]);
            $this->dispatch('showArtikelsuche', $this->suchArtikelnr, $this->suchBezeichnung);
        }
        //else
          //  Log::info('updateSuche = null; ',);

    }

    public function clickWarengruppe($wg, $sortiment){

        $this->aktiveWarengruppe = $wg;
        Log::info('Clickwarengruppe: ', ['$wg' => $this->aktiveWarengruppe, '$sortiment' => $sortiment]);

        $mWg = Warengruppe::where('wgnr', $wg)->first();
        $this->aktiveWarengruppeBezeichung = $mWg->bezeichnung;


        $this->dispatch('showArtikelWG', $wg, $sortiment);
    }

    #[On('showArtikel')]
    public function showArtikel($artikelnr){
        $this->mArtikel = Artikel::where('artikelnr', $artikelnr)->first();
        // Log::info('ShowArtikel', [$this->mArtikel] ) ;
        $this->showForm = true ;

    }

    public function changeTab($tab){
        $this->activeTab = $tab;
        session()->put('activeTab', $tab);
        debugLog(1, 'ChangeTab - activeTab', [session()->get('activeTab')]);

    }

    public function neuerFavorit(){
        $this->showFavoritForm = true ;
        $this->favoritId = -1;
        $this->favoritName = '';
        $this->favoritUser = false;
    }

    public function saveFavorit(){
        $userId = null;

        debugLog(1,'favoritUser',[$this->favoritUser]);

        $favoritUserID = 0;
        if ($this->favoritUser){
            $favoritUserID = Auth::id();
        }

        $favorite = Favorit::updateOrCreate(
            ['id' => $this->favoritId], // Suchkriterien: Wenn `id` existiert, wird der Datensatz aktualisiert.
            [
                'name' => $this->favoritName,
                'kundennr' => Auth::user()->kundennr,
                'user_id' => $favoritUserID
            ]
        );

        $this->favoriten = Favorit::cFavoriten(true);

        $this->showFavoritForm = false ;
        $this->isModified = false ;
    }


    public function selectFavorit($id){

        $this->aktiveFavorites = $id;

        //Log::info('selectFavorit', [$id]);
        $this->dispatch('showFavoritMitID', [ 'favoritId' => $id] );
    }


    public function editFavorit($id){
        $this->isModified = true ;
        $favorit = Favorit::where('id', $id)->first();
        $this->favoritId = $favorit->id;
        $this->favoritName = $favorit->name;
        $this->favoritUser = $favorit->user_id;

        $this->showFavoritForm = true ;
    }

    public function deleteFavorit($id){
        $this->isModified = true ;
        FavoritPos::where('favorit_id', $id)->delete();
        Favorit::where('id', $id)->delete();
        $this->showFavoritForm = true ;
    }


    #[On('showFavoritPosForm')]
    public function showFavoritPosForm($artikelnr){

        /*
            Formular mit der Auswahl in welche Favoriten der Artikel vorhanden sein soll, wird angezeigt.
            $this->dispatch('showFavoritPosForm' , ['artikelnr' => $artikelnr ]);
        */


        $this->artikelnr = $artikelnr;
        $this->mArtikel = Artikel::where('artikelnr', $artikelnr)->first();
        $this->favoriten = Favorit::cFavoriten();

        Log::info('Favoriten: ', [ $this->favoriten]);


        $favIDs = FavoritPos::getFavoritenIDs($artikelnr);

        foreach ($this->favoriten as $key => $favorit) {
            $this->favoritenIDs[$key] = in_array($key, $favIDs);
        }

        $this->zeigeFavoritPosForm = true ;
        $this->isModified = false ;

    }

    public function saveFavoritPos(){
        // Log::info('Artikelnr', [ $this->artikelnr ]);
        Foreach ($this->favoritenIDs as $key => $favorit){
            if ($this->favoritenIDs[$key] === false){
                FavoritPos::where('favoriten_id', $key)->where('artikelnr', $this->artikelnr)->delete();
                // Log::info('favorit wird gelöscht favoriten_id, artikelnr: ',  [$key, $this->artikelnr]);
            }
            else{
                Log::info('key', [ $key ]);
                $favorite = FavoritPos::create(
                    [ 'favoriten_id' => $key,
                        'artikelnr' => $this->artikelnr,
                    ]
                );
            }
        }
        $this->dispatch('renderShopArtikellisteComponent');
        $this->zeigeFavoritPosForm = false ;
        $this->isModified = false ;

    }

    #[On('zeigeMessage')]
    public function zeigeMessage(){
        $this->zeigeMessage = true ;
    }


    #[On('ShopComponent_NeueBestellung')]

    public function shopComponent_NeueBestellung(){


        $bestellung = Bestellung::getBasket();
        $this->activeTab = 'tab1';

        if ($bestellung){
            session()->put('bestellnr', $bestellung->nr);
            session()->put('activeTab', 'tab1');
        }

        $this->dispatch('updateNavigation');
        $this->dispatch('zeigeMessage');
    }

}
