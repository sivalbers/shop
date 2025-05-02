<?php

namespace App\Livewire;
use App\Models\Nachricht;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class NachrichtenListComponent extends Component
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
            })
            ->where(function ($query) use ($today) {
                $query->where('bis', '>=', $today)
                      ->orWhereNull('bis');
            });


            $this->nachrichten =$qu->orderBy('created_at', 'desc')->get();

    }

    public function render(){
        return view('livewire.nachrichten-list-component');
    }


}
