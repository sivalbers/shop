<?php

namespace App\Livewire;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Bestellung;
use App\Models\BestellungPos;
use App\Models\Anschrift;
use App\Models\Nachricht;
use Livewire\Attributes\On;
use App\Jobs\SendBestellungToErp;
use App\Mail\BestellbestaetigungMail;


use App\Repositories\BestellungRepository;

class WarenkorbComponent extends Component
{
    public $sortiment;
    public $kundenbestellnr;
    public $kommission;
    public $bemerkung;
    public $lieferdatum;
    public $minLieferdatum;
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
        Log::info([ '1 Lieferdatum' => $this->bestellung->lieferdatum->format('Y-m-d')] );
        $this->lieferdatum = $this->bestellung->lieferdatum ? $this->bestellung->lieferdatum->format('Y-m-d') : null;
        $this->minLieferdatum = Bestellung::calcLFDatum()->format('Y-m-d');
        if (empty($this->lieferdatum) | $this->lieferdatum == ''){
            $this->lieferdatum = Bestellung::calcLFDatum()->format('Y-m-d');
        }
        Log::info([ '2 Lieferdatum' => $this->lieferdatum ] );
    }


    private function getNachrichten(){

        $isAuth = Auth::check();

        // Heutiges Datum
        $today = date('Y-m-d');

        // Nachrichten aus der Datenbank laden

        $qu = Nachricht::where(function ($query) use ($today) {
            $query->where('von', '<=', $today)
                    ->orWhereNull('von');
        })
        ->where('mail', 1)
        ->where(function ($query) use ($isAuth) {
            $query->where('mitlogin', false)
                    ->orWhere(function ($query) use ($isAuth) {
                        $query->where('mitlogin', true)
                            ->whereRaw('? = true', [$isAuth]);
                    });
        });


        return $qu->get();
    }


    #[On('bestellungAbsenden')]
    public function bestellungAbsenden(){


        Log::info('Warenkorbkomponent->bestellungAbsenden');

        $this->bestellung->kundenbestellnr = $this->kundenbestellnr;
        $this->bestellung->kommission = $this->kommission;
        $this->bestellung->bemerkung = $this->bemerkung;
        $this->bestellung->lieferdatum = ($this->lieferdatum === '') ? null: $this->lieferdatum;
        $this->bestellung->status_id = 1;
        $this->bestellung->save();

        SendBestellungToErp::dispatch($this->bestellung); // Bestellung in warteschlange zum übertragen an faveo

        $nachrichten = $this->getNachrichten();

        $details = [
            'bestellung' => $this->bestellung,
            'nachrichten' => $nachrichten,
            'login' => Auth::user()->login,
        ];


        Mail::send(new BestellbestaetigungMail($details));

        $this->dispatch('ShopComponent_NeueBestellung');

    }


    #[On('updateWarenkorb')]
    public function updateWarenkorb($doShowMessage = true){


        $this->bestellung->kundenbestellnr = $this->kundenbestellnr;
        $this->bestellung->kommission = $this->kommission;
        $this->bestellung->bemerkung = $this->bemerkung;
        $this->bestellung->lieferdatum = ($this->lieferdatum === '') ? null: $this->lieferdatum;
        $this->bestellung->save();

        if ($doShowMessage){
            $this->dispatch('zeigeMessage', titel:"Angaben gespeichert.", hinweis: "Die Bestellung wurde noch nicht versendet.");
        }

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
        $this->bestellung->lieferdatum = Bestellung::calcLFDate();
        $this->bestellung->save();
        $this->setData();
        $this->dispatch('updateNavigation');
        $this->dispatch('doRefreshPositionen');
    }
}
