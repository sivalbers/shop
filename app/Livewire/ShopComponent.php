<?php

namespace App\Livewire;


use Livewire\Component;
use App\Services\FavoritenPosImporter;
use Livewire\WithFileUploads;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


use Livewire\Attributes\On;

use App\Services\FavoritenPosExporter;


use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\Sortiment;
use App\Models\Bestellung;
use App\Models\BestellungPos;
use App\Models\Anschrift;
use App\Models\Position;
use App\Models\Favorit;
use App\Models\FavoritPos;

use PhpParser\Node\Stmt\Foreach_;

use function PHPUnit\Framework\isEmpty;

class ShopComponent extends Component
{
    use WithFileUploads;
    /**
     * Get the view / contents that represent the component.
     */
    public $warengruppen;
    public $sortiment = 'EWE';

    public $aktiveWarengruppe;
    public $aktiveWarengruppeBezeichung = '';
    public $aktiveFavorites;
    public $showForm;
    public $showFavoritBearbeitenForm;
    public $showFavoritArtikelForm = false;
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


    public int|string|null $favoritId = null;

    public $favArtikel;
    public $favoritName;
    public $favoritUser;
    public $favoriten = [];

    public $favoritenIDs = [];

    public $pendingUpdateSuche;
    public $quantity;
    public $zeigeJaNeinAbfrage;

    public $importFile;
    public $showFavoritenPosImportModal = false;
    public $importOverride = false;

    public $aPositions = [];

    public function mount(){

        $this->showFavoritBearbeitenForm = false;
        $this->showFavoritArtikelForm = false;
        $this->suchArtikelnr = '';
        $this->suchBezeichnung = '';

        $this->sortiment = $this->session_get('mount', 'sortiment');
        $this->aktiveWarengruppe = configGet('aktiveWarengruppe' );
        $this->aktiveWarengruppeBezeichung = Warengruppe::getBezeichnung($this->aktiveWarengruppe);

        $this->aktiveFavorites = configGet('aktiveFavorites');
        $this->favoritName = '';


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

    public function render(){

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

            if ((empty($this->aktiveWarengruppe) || $this->aktiveWarengruppe === '') & (count($this->warengruppen) > 0)) {

               // dd($this->warengruppen);
                $this->aktiveWarengruppe = $this->warengruppen[0]['wgnr'];
                $this->aktiveWarengruppeBezeichung = Warengruppe::getBezeichnung($this->aktiveWarengruppe);
                configSet('aktiveWarengruppe', $this->aktiveWarengruppe);

            }

            if ($this->aktiveWarengruppe){
                $this->dispatch('showArtikelWG', $this->aktiveWarengruppe );
            }

        }
        elseif ($this->activeTab === 'favoriten') {
            $this->favoriten = Favorit::cFavoriten();
            $fId = configGet('aktiveFavorites');
            $this->dispatch('showFavoritMitID', $fId );
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
        $this->aktiveWarengruppeBezeichung = Warengruppe::getBezeichnung($wg);

        configSet('aktiveWarengruppe', $this->aktiveWarengruppe);

        Log::info(['aktiveWarengruppeBezeichnung' =>$this->aktiveWarengruppeBezeichung] );

        $this->dispatch('showArtikelWG', $wg );

    }

    #[On('showArtikel')]
    public function showArtikel($artikelnr){
        $this->mArtikel = Artikel::with('ersatzArtikel')->with('zubehoerArtikel')->where('artikelnr', $artikelnr)->first();
        $artikel = $this->mArtikel;

        $this->aPositions = [];

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



        foreach($this->mArtikel->zubehoerArtikel as $zubehoerArtikel){

            $this->aPositions[] = [
                'uid' => md5($zubehoerArtikel->zubehoerArtikel->artikelnr . now()),
                'id' => 0,
                'menge' => 0,
                'artikelnr' => $zubehoerArtikel->zubehoerartikelnr,
                'bezeichnung' => $zubehoerArtikel->zubehoerArtikel->bezeichnung,
                'vkpreis' => $zubehoerArtikel->zubehoerArtikel->vkpreis,
                'einheit' => $zubehoerArtikel->zubehoerArtikel->einheit,
                'steuer' => $zubehoerArtikel->zubehoerArtikel->steuer,
                'bestand' =>  $zubehoerArtikel->zubehoerArtikel->bestand,
                'langtext' =>  $zubehoerArtikel->zubehoerArtikel->langtext,
                'is_favorit' => false,
                'art' => 'Zubehörartikel',
            ] ;
        }


        foreach($this->mArtikel->ersatzArtikel as $ersatzartikel){
            $this->aPositions[] = [
                'uid' => md5($ersatzartikel->ersatzArtikel->artikelnr . now()),
                'id' => 0,
                'menge' => 0,
                'artikelnr' => $ersatzartikel->ersatzartikelnr,
                'bezeichnung' => $ersatzartikel->ersatzArtikel->bezeichnung,
                'vkpreis' => $ersatzartikel->ersatzArtikel->vkpreis,
                'einheit' => $ersatzartikel->ersatzArtikel->einheit,
                'steuer' => $ersatzartikel->ersatzArtikel->steuer,
                'bestand' =>  $ersatzartikel->ersatzArtikel->bestand,
                'langtext' =>  $ersatzartikel->ersatzArtikel->langtext,
                'is_favorit' => false,
                'art' => 'Ersatzartikel',
            ] ;
        }


        $this->quantity = 0;
        $this->showForm = true ;
        $this->refreshArtikelDetailFavoriten();

    }

    public function refreshArtikelDetailFavoriten(){
        if (!$this->showForm)
          return;

        $artikelNr = [];
        foreach($this->aPositions as $pos){
            $artikelNr[] =  $pos['artikelnr'];
        }

        $kundennr = session()->get('debitornr');
        $userId = Auth::id();

        $favoriten = DB::table('favoriten_pos as f_p')
            ->leftJoin('favoriten as f', function ($join) use ($kundennr, $userId) {
                $join->on('f.id', '=', 'f_p.favoriten_id')
                    ->where('f.kundennr', '=', $kundennr)
                    ->where(function ($query) use ($userId) {
                        $query->where('f.user_id', '=', 0)
                            ->orWhere('f.user_id', '=', $userId);
                    });
            })
            ->select('f_p.artikelnr', DB::raw('COUNT(f.id) > 0 AS is_favorit'))
            ->whereIn('f_p.artikelnr', $artikelNr)
            ->groupBy('f_p.artikelnr');

    //Log::info($favoriten->toRawSql());

        $favoriten = $favoriten->get();

        $favoritenMap = collect($favoriten)->keyBy('artikelnr');

        // 2. Durch aPositions iterieren und 'is_favorit' setzen
        foreach ($this->aPositions as &$pos) {
            $artikelnr = $pos['artikelnr'];
            $pos['is_favorit'] = false;
            if ($favoritenMap->has($artikelnr)) {
                $pos['is_favorit'] = (bool)$favoritenMap[$artikelnr]->is_favorit;
            }
        }

        return $favoriten;

    }

    public function changeTab($tab){



        $oldTab = $this->activeTab;

        $this->activeTab = $tab;
        $this->session_put('changeTab', 'activeTab', $tab);

        if ($tab === 'warengruppen'){
            $this->aktiveWarengruppe = configGet('aktiveWarengruppe');
            $this->aktiveWarengruppeBezeichung = Warengruppe::getBezeichnung($this->aktiveWarengruppe);
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
        $this->showFavoritBearbeitenForm = true ;
        $this->favoritId = -1;
        $this->favoritName = '';
        $this->favoritUser = false;
        $this->isModified = false ;
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

        $this->showFavoritBearbeitenForm = false ;
        $this->isModified = false ;
    }

    public function selectFavorit($id){

        $this->aktiveFavorites = $id;
        configSet('aktiveFavorites', $this->aktiveFavorites);

        $this->dispatch('showFavoritMitID', $id );
    }

    public function editFavorit($id){
        $this->isModified = true ;
        $favorit = Favorit::where('id', $id)->first();
        $this->favoritId = $favorit->id;
        $this->favoritName = $favorit->name;
        $this->favoritUser = $favorit->user_id;

        $this->showFavoritBearbeitenForm = true ;
    }

    public function deleteFavorit($id){
        FavoritPos::where('favoriten_id', $id)->delete();
        Favorit::where('id', $id)->delete();
    }

    public function abfrageLoeschungFavorit($id){
        $favorit = Favorit::where('id', $id)->first();

        $this->favoritId = $favorit->id;
        $this->favoritName = $favorit->name;

        $this->zeigeJaNeinAbfrage = true;

    }

    public function jaBestaetigt(){
        $this->deleteFavorit($this->favoritId);
        $this->zeigeJaNeinAbfrage = false;
        $this->favoriten = Favorit::cFavoriten(true);

       //  $this->dispatch('zeigeMessage', titel: "Favorit bearbeiten", hinweis: "Favorit wurde gelöscht.");
        // session()->flash('message', 'Favorit wurde gelöscht.');
    }
    public function neinBestaetigt(){

        $this->zeigeJaNeinAbfrage = false;

    }

    public function favoritenDownload($favoritenId) {
        $favorit = Favorit::findOrFail($favoritenId);

        $fileName = sprintf('favoriten_%s_%s.csv', $favorit->name, now()->format('Ymd_His') );

        $exporter = new FavoritenPosExporter();

        return response()->streamDownload(function () use ($exporter, $favoritenId) {
            $handle = fopen('php://output', 'w');
            $exporter->exportSingle($favoritenId, $handle);
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function favoritenPosImport(){
        Log::info('favoritenPosImport');
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt',
        ]);

        $importer = new FavoritenPosImporter();
        $importer->importFromFile($this->favoritId, $this->importOverride, $this->importFile->getRealPath());

        $this->reset('importFile', 'showFavoritenPosImportModal', 'importFile', 'importOverride');

        session()->flash('success', 'Import erfolgreich!');

        $this->dispatch('showFavoritMitID', $this->favoritId );


    }

    #[On('favoritArtikelForm')]
    public function favoritArtikelForm($artikelnr){

        /*
            Formular mit der Auswahl in welche Favoriten der Artikel vorhanden sein soll, wird angezeigt.
            $this->dispatch('favoritArtikelForm' , ['artikelnr' => $artikelnr ]);
        */


        $this->artikelnr = $artikelnr;
        $this->favArtikel = Artikel::where('artikelnr', $artikelnr)->first();
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

        $this->showFavoritArtikelForm = true ;
        // $this->isModified = false ;

    }

    #[On('favoritArtikelDelete')]
    public function favoritArtikelDelete($id){

        $favoritPos = FavoritPos::where('id', $id)->first();

        $favoritId = $favoritPos->favoriten_id;

        $favoritPos->delete();
        $this->dispatch('showFavoritMitID', $favoritId );
    }

    public function saveFavoritArtikel(){

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
        $this->showFavoritArtikelForm = false ;
        $this->isModified = false ;
        $this->refreshArtikelDetailFavoriten();
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

    public function InBasket(){
        Log::info('ShopComponent.InBasket');
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



}
