<?php

namespace App\Livewire;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Bestellung;
use App\Models\BestellungPos;
use App\Models\Anschrift;
use Livewire\Attributes\On;


class WarenkorbComponent extends Component
{
    public $sortiment;
    public $kundenbestellnr;
    public $kommission;
    public $bemerkung;
    public $lieferdatum;
    public $bestellung;
    public $rechnungsadresse;
    public $lieferadresse;

    public function mount($sortiment){

        $this->bestellung = Bestellung::getBasket();
        $this->sortiment = $sortiment;

        $this->setData();

        $this->rechnungsadresse = Anschrift::getAdresseFormat($this->bestellung->rechnungsadresse);
        $this->lieferadresse = Anschrift::getAdresseFormat($this->bestellung->lieferadresse);
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view('livewire.shop.shopWarenkorb');
    }

    private function setData(){
        $this->kundenbestellnr = $this->bestellung->kundenbestellnr;
        $this->kommission = $this->bestellung->kommission;
        $this->bemerkung = $this->bestellung->bemerkung;
        $this->lieferdatum = $this->bestellung->lieferdatum;
    }

    #[On('updateWarenkorb')]
    public function updateWarenkorb($updatePos = false){

        Log::info('Warenkorbkomponent->updateWarenkorb');

        $this->bestellung->kundenbestellnr = $this->kundenbestellnr;
        $this->bestellung->kommission = $this->kommission;
        $this->bestellung->bemerkung = $this->bemerkung;
        $this->bestellung->lieferdatum = $this->lieferdatum;

        $this->bestellung->save();

    }


    public function doNullMengenEntfernen(){
        BestellungPos::where('bestellnr', $this->bestellung->nr)->where('menge', 0)->delete();
        $this->dispatch('updateNavigation');
        $this->dispatch('refresh-page');

    }


    public function doEmpty(){
        BestellungPos::where('bestellnr', $this->bestellung->nr)->delete();
        $this->bestellung->kundenbestellnr = '';
        $this->bestellung->kommission = '';
        $this->bestellung->bemerkung = '';
        $this->bestellung->lieferdatum = null;
        $this->bestellung->save();
        $this->setData();
        $this->dispatch('updateNavigation');
        $this->dispatch('doRefreshPositionen');
    }
}
