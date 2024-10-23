<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Position;
use Livewire\Attributes\On;



class BestellungPositionComponent extends Component
{

    public $positionen;
    public $bestellnr;

    public function mount()

    {
        

    }



    #[On('loadPositionen')]
    public function loadPositionen($bestellnr)
    {
        // Lade die Positionen der Bestellung anhand der Bestellnummer

        if (!empty($this->bestellnr = $bestellnr))
            $this->positionen = Position::where('bestellnr', $this->bestellnr)->get();
        else
            $this->positionen = Position::where('bestellnr', '')->get();
    }



    public function render()
    {
        return view('livewire.bestellung-position-component');
    }


}
