<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ODataController;


class ImportComponent extends Component
{

    public function import($type)
    {
        if ($type === 'Artikel') {
            $this->importArtikel();
        }
        else{
            $response = Http::get(route("import{$type}"));
            session()->flash('message', $response->json()['message'] ?? 'Fehler beim Import');
        }
    }

   public function render()
    {
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


}
