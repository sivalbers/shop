<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Bestellung;
use App\Models\User;



class BestellungComponent extends Component
{

    public $bestellungen;
    public $selectedBestellung = null;
    public $activeBestellung = null;
    public $bestellung;

    public function mount()
    {
        Log::info('Bestellcomponent-mount()');
        $this->loadData();
    }


    public function loadData(){
        Log::info('Bestellcomponent-loadData()');
        $user = Auth::user();

        $qu = Bestellung::select(
            'nr',
            'datum',
            'bestellungen.kundennr',
            'status.bezeichnung as status',
            'users.login as besteller',
            'gesamtbetrag',
            'lieferdatum',
            'rechnungsadresse.kurzbeschreibung as rechnungsadresse',
            'lieferadresse.kurzbeschreibung as lieferadresse',
//            'status.bezeichnung as status_bezeichnung'
        )
        ->where('bestellungen.kundennr', $user->kundennr)
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

        foreach( $data as $item){
            $this->bestellungen[] = [
                'nr' => $item->nr,
                'datum' => $item->datum,
                'status' => $item->status,
                'besteller' => $item->besteller,
                'gesamtbetrag' => $item->gesamtbetrag,
                'rechnungsadresse' => $item->rechnungsadresse,
                'lieferadresse' => $item->lieferadresse, ];
        };

        if (empty($this->activeBestellung)){
            $this->loadPositionen( $this->bestellungen[0]['nr'] );
        }
    }

    public function render()
    {
        Log::info('Bestellcomponent-render()');

        return view('livewire.bestellung-component');
    }


    public function loadPositionen($bestellnr)
    {
        Log::info(['Bestellcomponent-loadPositionen()'=> $bestellnr]);

        // Lade die Positionen der Bestellung anhand der Bestellnummer

        $this->activeBestellung = Bestellung::where('nr', $bestellnr)->first();

        //$user = User::find($this->activeBestellung->user_id);

        Log::info([ 'Vor-Dispatch-loadPositionen', 'activeBestellung->nr' => $this->activeBestellung->nr ]);
        $this->dispatch('loadPositionen', $bestellnr );
    }

}
