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
    public $messageTitel;
    public $messageHinweis;

    public $artikelnr;
    public $isModified ;

    public $countBasket;

    public $mArtikel = null;

    public $quantities = null; // Array zum Speichern der Mengen für jeden Artikel

    public $activeTab = 'warengruppen';

    protected $queryString = ['activeTab' => ['except' => 'warengruppen']];

    public $suchArtikelnr = '';
    public $suchBezeichnung = '';
    public $expanded = false ;

    public $favoritId;
    public $favoritName;
    public $favoritUser;
    public $favoriten = [];

    public $favoritenIDs = [];

    public $pendingUpdateSuche;

    public function mount()
    {



        $this->showFavoritForm = false;
        $this->zeigeFavoritPosForm = false;
        $this->suchArtikelnr = '';
        $this->suchBezeichnung = '';

        $this->sortiment = $this->session_get('mount', 'sortiment');
        $this->aktiveWarengruppe = configGet('aktiveWarengruppe' );
        $this->aktiveFavorites = configGet('aktiveFavorites');


        $tab = request()->query('tab', '');

        if (!empty($tab)){
            $this->session_put('mount()', 'activeTab', $tab);
            $this->activeTab = $tab;
        }

        $this->updateQuery();

        if (request()->has('artikel')) {
            Log::info(['showArtikel' => request()->get('artikel')]);
            $this->suchBezeichnung = request()->get('suchBezeichnung');
            $this->pendingUpdateSuche = true; // Setze eine Flag
            Log::info('ShopComponent.Mount()');
            $this->dispatch('showArtikel', artikelnr: request()->get('artikel'));
        }
        request()->get('artikel');
    }

    public function render()
    {

        if (($this->activeTab === 'warengruppen' && empty($this->warengruppen)) ||
            ($this->activeTab === 'favoriten' && empty($this->favoriten))
           ){
            $this->updateQuery();
        }

        $startTime = microtime(true);

        if ($this->pendingUpdateSuche) {
            Log::info('ShopComponent.render()');
            $this->updateSuche();
            $this->pendingUpdateSuche = false; // Flag zurücksetzen
        }


        $view = view('livewire.shop.shopmain');
        $endTime = microtime(true);

         $duration = ($endTime - $startTime) * 1000;


        return $view;
    }


    public function updateQuery(){


        if ($this->activeTab === 'warengruppen') {
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

            $results = $query->get();

            $this->warengruppen = [];

            $results->each(function($item) use (&$wg) {
                $this->warengruppen[] = [
                    'wgnr' => $item->wgnr,
                    'bezeichnung' => $item->bezeichnung,
                    'artikel_count' => $item->artikel_count,
                ];
            });

            if (empty($this->aktiveWarengruppe) || $this->aktiveWarengruppe === ''){

                $this->aktiveWarengruppe = $this->warengruppen[0]['wgnr'];
                configSet('aktiveWarengruppe', $this->aktiveWarengruppe);

            }

            if ($this->aktiveWarengruppe){

                if (is_array($this->aktiveWarengruppe)){
                    dd($this->aktiveWarengruppe);
                }

                $this->dispatch('showArtikelWG', $this->aktiveWarengruppe );
            }

        }
        elseif ($this->activeTab === 'favoriten') {
            $this->favoriten = Favorit::cFavoriten();
            $fId = configGet('aktiveFavorites');
            $this->dispatch('showFavoritMitID', [ 'favoritId' => $fId] );
        }
        elseif ($this->activeTab === 'suche') {
            $this->updateSuche();
        }
    }


    public function updatedSuchText($value){
    }

    public function updateSuche(){

        $this->dispatch('showArtikelsuche', $this->suchArtikelnr, $this->suchBezeichnung);

    }

    public function clickWarengruppe($wg){

        $this->aktiveWarengruppe = $wg;
        // $this->session_put('clickWarengruppe', session()->get('debitornr').'.aktiveWarengruppe', $this->aktiveWarengruppe);
        configSet('aktiveWarengruppe', $this->aktiveWarengruppe);



        $xxWG = configGet('aktiveWarengruppe');

        $mWg = Warengruppe::where('wgnr', $wg)->first();
        $this->aktiveWarengruppeBezeichung = $mWg->bezeichnung;


        if (is_array($wg)){
            dd($wg);
        }

        $this->dispatch('showArtikelWG', $wg );

    }

    #[On('showArtikel')]
    public function showArtikel($artikelnr){
        $this->mArtikel = Artikel::where('artikelnr', $artikelnr)->first();

        $this->showForm = true ;

    }

    public function changeTab($tab){



        $oldTab = $this->activeTab;

        $this->activeTab = $tab;
        $this->session_put('changeTab', 'activeTab', $tab);

        if ($tab === 'warengruppen'){
            $this->aktiveWarengruppe = configGet('aktiveWarengruppe');
        }

        if ($tab === 'favoriten'){
            $this->aktiveFavorites = configGet('aktiveFavorites');
        }

        $this->dispatch('clearArtikelliste', $this->activeTab);

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
        if (count($this->favoriten)===0){
            $user = Auth::user();
            Favorit::create([
                'kundennr' => session()->get('debitornr'),
                'user_id' => $user->id,
                'name' => $user->name,
            ]);
            $this->favoriten = Favorit::cFavoriten();
        }

        $favIDs = FavoritPos::getFavoritenIDs($artikelnr);

        foreach ($this->favoriten as $key => $favorit) {
            $this->favoritenIDs[$key] = in_array($key, $favIDs);
        }

        $this->zeigeFavoritPosForm = true ;
        $this->isModified = false ;

    }

    public function saveFavoritPos(){

        Foreach ($this->favoritenIDs as $key => $favorit){
            if ($this->favoritenIDs[$key] === false){
                FavoritPos::where('favoriten_id', $key)->where('artikelnr', $this->artikelnr)->delete();

            }
            else{

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
    public function zeigeMessage($titel = '', $hinweis = ''){
        $this->messageTitel = $titel;
        $this->messageHinweis = $hinweis;
        $this->zeigeMessage = true ;

    }


    #[On('ShopComponent_NeueBestellung')]

    public function shopComponent_NeueBestellung(){


        $bestellung = Bestellung::getBasket();
        $this->activeTab = 'warengruppen';

        if ($bestellung){

            $this->session_put('shopComponent_NeueBestellung', 'bestellnr', $bestellung->nr );
            $this->session_put('shopComponent_NeueBestellung', 'activeTab', 'warengruppen' );
        }

        $this->dispatch('updateNavigation');
        $this->dispatch('zeigeMessage', titel: "Bestellung wurde versendet!", hinweis: "Ihre Bestellbestätigung erhalten Sie in kürze per E-Mail.");
    }

    public function session_put($func, $name, $value){
        // Log::info([$func => 'session()->put(', $name => $value] );
        session()->put($name, $value);
    }

    public function session_get($func, $name){

        $value = session()->get($name);
        // Log::info([$func => 'session()->get(', $name => $value] );
        return $value ;
    }


}
