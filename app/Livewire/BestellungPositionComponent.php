<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Bestellung;
use App\Models\BestellungPos;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;



class BestellungPositionComponent extends Component
{

    public $positionen;
    public $bestellnr;

    public $markiertePositionen = [];
    public $markiereAlle = false;

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
        $this->markiertePositionen = [];
        $this->markiereAlle = false ;
    }

    public function render()
    {
        return view('livewire.bestellung-position-component');
    }

    public function toggleAlleCheckboxen()
    {
        if ($this->markiereAlle) {
            // Alle Positionen auswählen
            $this->markiertePositionen = collect($this->positionen)->pluck('id')->toArray();
        } else {
            // Alle Auswahl aufheben
            $this->markiertePositionen = [];
        }
    }

    public function markierteBestellen()
    {

        if (!empty($this->markiertePositionen)) {
            $bestellung = Bestellung::getBasket();

            foreach ($this->markiertePositionen as $data) {
                $pos = BestellungPos::where('id', $data)->first();

                BestellungPos::Create([
                    'bestellnr' => $bestellung->nr,
                    'artikelnr' => $pos->artikelnr,
                    'menge' => $pos->menge,
                    'epreis' => $pos->epreis,
                    'gpreis' => $pos->gpreis,
                    'steuer' => $pos->steuer,
                    'sort' => 0,
                ]);
            }
            $this->markiertePositionen = [];
            $this->markiereAlle = false;

            $this->dispatch('updateNavigation');

        } else {
            session()->flash('message', 'Keine Positionen ausgewählt!');
        }
    }

}
