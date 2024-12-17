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


use App\Mail\BestellbestaetigungMail;

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
        // $this->lieferdatum = $this->bestellung->lieferdatum ? $this->bestellung->lieferdatum->format('Y-m-d') : null;
        $this->lieferdatum = optional($this->bestellung->lieferdatum)->format('Y-m-d');
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


    #[On('updateWarenkorb')]
    public function updateWarenkorb($doSend = false){

        Log::info('Warenkorbkomponent->updateWarenkorb');

        $this->bestellung->kundenbestellnr = $this->kundenbestellnr;
        $this->bestellung->kommission = $this->kommission;
        $this->bestellung->bemerkung = $this->bemerkung;
        $this->bestellung->lieferdatum = ($this->lieferdatum === '') ? null: $this->lieferdatum;
        $this->bestellung->save();


        $nachrichten = $this->getNachrichten();

    $details = [
        'bestellung' => $this->bestellung,
        'nachrichten' => $nachrichten,
        'title' => 'Willkommen bei Laravel!',
        'body' => 'Dies ist ein Beispieltext für eine E-Mail.',
        'login' => Auth::user()->login,
    ];


    // Mail::to($mail)->to("mail@andreasalbers.de")->send(new BestellbestaetigungMail($details));
    Mail::send(new BestellbestaetigungMail($details));
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
