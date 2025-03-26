<?php

namespace App\Livewire;


use Livewire\Component;
// use Illuminate\Support\Facades\Log;
use App\Models\Bestellung;

use App\Repositories\BestellungRepository;

class BestellungComponent extends Component
{

    public $bestellungen;
    public $selectedBestellung = null;
    public $activeBestellung = null;
    public $bestellung;

    public function mount()
    {

        $this->loadData();
    }


    public function loadData(){
        $kundennr = Session()->get('debitornr');


        $qu = Bestellung::select(
            'nr',
            'datum',
            'bestellungen.kundennr',
            'status.bezeichnung as status',
            'users.name as besteller',
            'gesamtbetrag',
            'lieferdatum',
            'erpid',
            'rechnungsadresse.kurzbeschreibung as rechnungsadresse',
            'lieferadresse.kurzbeschreibung as lieferadresse',
//            'status.bezeichnung as status_bezeichnung'
        )
        ->where('bestellungen.kundennr', $kundennr)
        ->where('status_id', '>', (int)0 )
        // Join für Rechnungsadresse
        ->leftJoin('anschriften as rechnungsadresse', 'rechnungsadresse', '=', 'rechnungsadresse.id')

        // Join für Lieferadresse
        ->leftJoin('anschriften as lieferadresse', 'lieferadresse', '=', 'lieferadresse.id')

        // Join für Status
        ->leftJoin('status', 'status_id', '=', 'status.id')

        ->leftJoin('users', 'user_id', '=', 'users.id');

        $qu = $qu->orderBy('datum', 'desc');

        // Ergebnis abrufen
        $data = $qu->get();
        $this->bestellungen = [];
        foreach( $data as $item){
            $this->bestellungen[] = [
                'nr' => $item->nr,
                'datum' => $item->datum,
                'status' => $item->status,
                'besteller' => $item->besteller,
                'gesamtbetrag' => $item->gesamtbetrag,
                'rechnungsadresse' => $item->rechnungsadresse,
                'lieferadresse' => $item->lieferadresse,
                'erpid' => $item->erpid, ];
        };

        if (empty($this->activeBestellung) ){
            if (count($this->bestellungen)>0) {
                $this->loadPositionen( $this->bestellungen[0]['nr'] );
            }
        }
    }

    public function render()
    {
        return view('livewire.bestellung-component');
    }


    public function loadPositionen($bestellnr)
    {
        // Lade die Positionen der Bestellung anhand der Bestellnummer

        $this->activeBestellung = Bestellung::where('nr', $bestellnr)->first();
        $this->dispatch('loadPositionen', $bestellnr );
    }


    public function bestellungErneutSenden($bestellnr){
        $best = Bestellung::where('nr', $bestellnr)->first();
        $br = new BestellungRepository();
        $br->sendToERP($best);
    }
}
