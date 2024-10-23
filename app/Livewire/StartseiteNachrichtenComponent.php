<?php

namespace App\Livewire;
use App\Models\Nachricht;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class StartseiteNachrichtenComponent extends Component
{
    public $nachrichten;

    public function mount(){

            // Prüfen, ob der Benutzer angemeldet ist
            $isAuth = Auth::check();

            // Heutiges Datum
            $today = date('Y-m-d');

            // Nachrichten aus der Datenbank laden

            $qu = Nachricht::where(function ($query) use ($today) {
                $query->where('von', '<=', $today)
                      ->orWhereNull('von');
            })
            ->where(function ($query) use ($isAuth) {
                $query->where('mitlogin', false)
                      ->orWhere(function ($query) use ($isAuth) {
                          $query->where('mitlogin', true)
                                ->whereRaw('? = true', [$isAuth]);
                      });
            });


            $this->nachrichten =$qu->get();

    }

    public function render(){
        return view('livewire.startseitenachrichten');
    }


}
