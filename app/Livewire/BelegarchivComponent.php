<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Repositories\BelegarchivRepository;


class BelegarchivComponent extends Component
{


    public $zeigeMessage = false ;
    public $messageTitel;
    public $messageHinweis;
    public $datumVon;
    public $datumBis;
    public $belege = [];

    public $belegeTyp = [];

    public function mount()
    {
        $this->loadBelege();
    }

    public function getDocType($id){
        $result = "";
        switch($id){
            case 1: $result = 'Rechnung';
                break;
            case 2: $result = 'Lieferschein';
                break;
            case 3: $result = 'Gutschrift';
                break;
            case 4: $result = 'Auftrag';
                break;
        }
        return $result;
    }


    private function loadBelege(){
        $this->datumVon = Carbon::now()->subMonths(12)->startOfDay();
        $this->datumBis = Carbon::now()->endOfDay();
        $br = new BelegarchivRepository();
        $data = $br->readBelegArchiv( $this->datumVon, $this->datumBis);

        $this->belege = json_decode( $data['data']) ;
        $this->sortBelegeNachDatumDesc();

        $tt = 0;
        foreach ($this->belege as $be){
            $tt = $be->documentType;
            if ( $tt === 3 )
              $tt = 1;
            $this->belegeTyp[$tt][] = [
              'typ' => $this->getDocType($be->documentType),
              'nr' => $be->documentNo,
              'datum' => Carbon::createFromFormat('Y-m-d', $be->documentDate) ,
              'netto' => $be->netAmount,
            ];
        }
    }

    public function sortBelegeNachDatum()
    {
        usort($this->belege, function ($a, $b) {
            return strtotime($a->documentDate) <=> strtotime($b->documentDate);
        });
    }

    public function sortBelegeNachDatumDesc()
    {
        usort($this->belege, function ($a, $b) {
            return strtotime($b->documentDate) <=> strtotime($a->documentDate);
        });
    }




    public function loadBeleg($belegNr){

        Log::info('LoadBeleg: '.$belegNr);

    }

    public function render()
    {
        return view('livewire.belegarchiv-component');
    }

}
