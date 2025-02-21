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
        debugLog(0, 'X ShopComponent.mount()');
        $this->showFavoritForm = false;
        $this->zeigeFavoritPosForm = false;
        $this->suchArtikelnr = '';
        $this->suchBezeichnung = '';

        $this->sortiment = $this->session_get('mount', 'sortiment');

        $this->aktiveWarengruppe = configGet('aktiveWarengruppe' );

        Log::info([ 'ShopComponent => mount() => (1) aktiveWarengruppe', '>'. $this->aktiveWarengruppe .'<']);

        $this->aktiveFavorites = configGet('aktiveFavorites');
        

        $tab = request()->query('tab', '');

        if (!empty($tab)){
            $this->session_put('mount()', 'activeTab', $tab);
            $this->activeTab = $tab;
        }

        $this->updateQuery();
    }

    public function render()
    {
        Log::info('...');
        Log::info('ShopComponent.Render() - Anfang', ['Sortiment' => $this->sortiment, 'activeTab' =>$this->activeTab ]);

        if (($this->activeTab === 'tab1' && empty($this->warengruppen)) ||
            ($this->activeTab === 'tab3' && empty($this->favoriten))
           ){
            $this->updateQuery();
        }

        $startTime = microtime(true);
        $view = view('livewire.shop.shopmain');
        $endTime = microtime(true);

         $duration = ($endTime - $startTime) * 1000;

        Log::info("ShopComponent.render.view Ende " , [ 'duration' => $duration]);
        return $view;
    }


    public function updateQuery(){

        Log::info('*');
        Log::info('*');
        Log::info('*');
        Log::info('*');
        Log::info('ShopComponent.updateQuery()', ['Sortiment' => $this->sortiment, 'activeTab' =>$this->activeTab ]);

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
            Log::info(['(0) Aktive Warengruppe' => $this->aktiveWarengruppe ]);
            if (empty($this->aktiveWarengruppe) || $this->aktiveWarengruppe === ''){
                Log::info(['(1) Aktive Warengruppe ist null ' => $this->aktiveWarengruppe ]);
                $this->aktiveWarengruppe = $this->warengruppen[0]['wgnr'];
                configSet('aktiveWarengruppe', $this->aktiveWarengruppe);
                Log::info(['(2) Aktive Warengruppe ist jetzt: ' => $this->aktiveWarengruppe ]);
            }

            if ($this->aktiveWarengruppe){
                Log::info('dispatch selectWarengruppe', [ $this->aktiveWarengruppe]);
                if (is_array($this->aktiveWarengruppe)){
                    dd($this->aktiveWarengruppe);
                }
                Log::info('showArtikelWG');
                $this->dispatch('showArtikelWG', $this->aktiveWarengruppe );
            }

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

    public function clickWarengruppe($wg){

        $this->aktiveWarengruppe = $wg;
        // $this->session_put('clickWarengruppe', session()->get('debitornr').'.aktiveWarengruppe', $this->aktiveWarengruppe);
        configSet('aktiveWarengruppe', $this->aktiveWarengruppe);

        Log::info('Clickwarengruppe: ', ['$wg' => $this->aktiveWarengruppe]);

        $xxWG = configGet('aktiveWarengruppe');
        Log::info('session AktiveWarengruppe: ', ['$xxWG' => $xxWG]);
        $mWg = Warengruppe::where('wgnr', $wg)->first();
        $this->aktiveWarengruppeBezeichung = $mWg->bezeichnung;

        Log::info('vor Dispatch => showArtikelWG');
        if (is_array($wg)){
            dd($wg);
        }

        $this->dispatch('showArtikelWG', $wg );
        Log::info('nach Dispatch => showArtikelWG');
    }

    #[On('showArtikel')]
    public function showArtikel($artikelnr){
        $this->mArtikel = Artikel::where('artikelnr', $artikelnr)->first();
        // Log::info('ShowArtikel', [$this->mArtikel] ) ;
        $this->showForm = true ;

    }

    public function changeTab($tab){

        $oldTab = $this->activeTab;

        $this->activeTab = $tab;
        $this->session_put('changeTab', 'activeTab', $tab);

        if ($tab === 'tab1'){
            $this->aktiveWarengruppe = configGet('aktiveWarengruppe');
        }

        if ($tab === 'tab3'){
            $this->aktiveFavorites = configGet('aktiveFavorites');
        }

        if ($oldTab !== $this->activeTab){
            $this->updateQuery();
        }

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
                'kundennr' => Session()->get('debitornr'),
                'user_id' => $favoritUserID
            ]
        );


        $this->favoriten = Favorit::cFavoriten(true);

        $this->showFavoritForm = false ;
        $this->isModified = false ;
    }


    public function selectFavorit($id){

        $this->aktiveFavorites = $id;
        configSet('aktiveFavorites', $this->aktiveFavorites);


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

            $this->session_put('shopComponent_NeueBestellung', 'bestellnr', $bestellung->nr );
            $this->session_put('shopComponent_NeueBestellung', 'activeTab', 'tab1' );
        }

        $this->dispatch('updateNavigation');
        $this->dispatch('zeigeMessage');
    }

    public function session_put($func, $name, $value){
        Log::info([$func => 'session()->put(', $name => $value] );
        session()->put($name, $value);
    }

    public function session_get($func, $name){

        $value = session()->get($name);
        Log::info([$func => 'session()->get(', $name => $value] );
        return $value ;
    }


}
