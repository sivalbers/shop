<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ODataController;
use App\Services\FavoritImportService;


class ImportComponent extends Component
{

    public float $progress = 0;
    public array $fehlerhafte = [];
    public array $fehlendeDebitoren = [];
    public int $importErfolgreich = 0;
    public int $importFehlerhaft = 0;

    public function import($type){

        session()->flash('message', 'Start');

        if ($type === 'Artikel') {
            $this->importArtikel();
        }
        elseif ($type === 'Favoriten') {
            $this->importFavoriten();
        }
        elseif ($type === 'Sortiment') {
                $odata = new ODataController();
                $response = $odata->importSortiment();
        }
        session()->flash('message', $response->json()['message'] ?? 'Fehler beim Import: '.$type);

    }

    public function render(){
      return view('livewire.import');

    }

    public function importArtikel(){

        $od = new ODataController();
        $response = $od->importArtikel();

        session()->flash('message', $response);
        return ;
        if ($response->json()['ok']){
            session()->flash('message', $response->json()['ok'] ??  $response->json()['error']);
        }


        $response =  [ 'message' => 'okay' ] ;
        session()->flash('message', $response['message'] ?? 'Fehler beim Import');
    }


    public function importFavoriten()
    {
        $service = new FavoritImportService();

        $result = $service->importFavoritFile(function ($fortschritt) {
            $this->progress = $fortschritt;
        });

        $this->importErfolgreich = $result['fehlerfrei'];
        $this->importFehlerhaft = $result['fehlerhaft'];
        $this->fehlerhafte = $result['fehlerhafte'];
        $this->fehlendeDebitoren = $result['debitoren_fehlen'];

        session()->flash('message', sprintf('Status: OK | Erfolgreich: %d | Fehlerhaft: %d', $this->importErfolgreich, $this->importFehlerhaft));
    }

}
