<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\BestellungPos;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;



class BestellungPositionComponent extends Component
{

    public $positionen;
    public $bestellnr;

    public function mount($bestellnr)
    {
        $this->bestellnr = $bestellnr;
        $this->loadPositionen($bestellnr);

        Log::info('BestellungPositionComponent-mount()');

    }



    #[On('loadPositionen')]
    public function loadPositionen($bestellnr)
    {
        Log::info(['BestellungPositionComponent-loadPositionen()'=> $bestellnr]);
        // Lade die Positionen der Bestellung anhand der Bestellnummer

        if (!empty($this->bestellnr = $bestellnr))
            $this->positionen = BestellungPos::where('bestellnr', $this->bestellnr)->get();
        else
            $this->positionen = BestellungPos::where('bestellnr', '')->get();
    }



    public function render()
    {
        return view('livewire.bestellung-position-component');
    }


}
