<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Artikel;
use App\Models\ArtikelSortiment;

/*
VERALTET


*/

class ShopArtikelsucheComponent extends Component
{
    public $suchWG = [];
    public $suchText;
    public $wgList;



    public function mount(){

    }

    public function render()
    {


        return view('livewire.shop.shopartikelsuche');
    }

    public function search($suchText, $sortimentArray){

        Log::info('shopartikelsuchecomponent');
        $sortiment = explode( ' ', session()->get('sortiment'));

        $this->dispatch('showArtikelSuche', $this->suchtText, $sortiment);



        $qu = Artikel::where(function ($query) use ($suchText) {
            $query->where('artikelnr', 'like', "%{$suchText}%")
                  ->orWhere('bezeichnung', 'like', "%{$suchText}%")
                  ->orWhere('langtext', 'like', "%{$suchText}%");
        })
        ->whereIn('artikelnr', ArtikelSortiment::whereIn('sortiment', $sortimentArray)->pluck('artikelnr'));

        Log::info($qu->getRawSql());

        return $qu->get();

    }
}
