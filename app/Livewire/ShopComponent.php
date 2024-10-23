<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;




use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\Sortiment;
use App\Models\Bestellung;
use App\Models\Anschrift;
use App\Models\Position;



class ShopComponent extends Component
{

    /**
     * Get the view / contents that represent the component.
     */
    public $warengruppen;
    public $sortiment = 'EWE';

    public $aktiveWarengruppe;
    public $aktiveWarengruppeBezeichung;
    public $showForm;

    public $artikelnr;
    public $isModified ;

    public $countBasket;


    public $mArtikel = null;

    public $quantities = null; // Array zum Speichern der Mengen für jeden Artikel

    public $activeTab = 'tab1';

    protected $queryString = ['activeTab' => ['except' => 'tab1']];

    public function mount()
    {
        $this->activeTab = request()->query('tab', 'tab1');
        
    }

    private function update(){
        // Log::info('render', [$this->sortiment]);
        $sortimentArray = explode ( ' & ', $this->sortiment);

        if (is_null($this->mArtikel)){
            $mArtikel = new Artikel();
        }

        if (is_null($this->quantities)){
            $this->quantities = array();
        }


        $query = Warengruppe::select('warengruppen.wgnr', 'warengruppen.bezeichnung', DB::raw('COUNT(artikels.artikelnr) AS artikel_count'))
        ->join('artikels', 'warengruppen.wgnr', '=', 'artikels.wgnr')
        ->join('artikel_sortimente', 'artikels.artikelnr', '=', 'artikel_sortimente.artikelnr')
        ->whereIn('artikel_sortimente.sortiment', $sortimentArray)
        ->groupBy('warengruppen.wgnr', 'warengruppen.bezeichnung')
        ->orderBy('warengruppen.bezeichnung');

        $this->warengruppen = $query->get();
        // Log::info('Render - Warengruppe: ', [$this->aktiveWarengruppe]);
        // dd($this->warengruppen);
    }

    public function render()
    {
        $this->update();
        return view('livewire.shop.shopmain');
    }


    public function clickWarengruppe($wg, $sortiment){

        $this->aktiveWarengruppe = $wg;
//        Log::info('Clickwarengruppe: ', [$this->aktiveWarengruppe]);

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
    }


}
