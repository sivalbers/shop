<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Bestellung;



class BestellungComponent extends Component
{

    public $bestellungen;
    public $selectedBestellung = null;
    public $activeBestellung = '';

    public function mount()
    {




    }

    public function render()
    {
        $user = Auth::user();

        $qu = Bestellung::select(
            'bestellung.nr',
            'bestellung.datum',
            'bestellung.kundennr',
            'users.name as besteller',
            'bestellung.gesamtbetrag',
            'bestellung.lieferdatum',
            'rechnungsadresse.kurzbeschreibung as rechnungsadresse',
            'lieferadresse.kurzbeschreibung as lieferadresse',
            'status.bezeichnung as status_bezeichnung'
        )
        // Join für Rechnungsadresse
        ->leftJoin('anschriften as rechnungsadresse', 'bestellung.rechnungsadresse', '=', 'rechnungsadresse.id')

        // Join für Lieferadresse
        ->leftJoin('anschriften as lieferadresse', 'bestellung.lieferadresse', '=', 'lieferadresse.id')

        // Join für Status
        ->leftJoin('status', 'bestellung.status', '=', 'status.id')

        ->leftJoin('users', 'bestellung.user_id', '=', 'users.id');

        if (!$user->isAdmin()){
            $qu = $qu->where('bestellung.kundennr', $user->kundennr);
        }

        $qu = $qu->orderBy('bestellung.datum', 'desc');

        // Ergebnis abrufen
        $this->bestellungen = $qu->get();
        return view('livewire.bestellung-component');
    }

    public function loadPositionen($bestellnr)
    {
        // Lade die Positionen der Bestellung anhand der Bestellnummer
        $this->activeBestellung = $bestellnr;
        $this->dispatch('loadPositionen', $bestellnr );
    }

}
