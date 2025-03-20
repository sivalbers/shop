<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\Form;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;

use App\Models\Nachricht;


class HeaderComponent extends Component
{

    public string $header = '';

    public $nachrichten;

    public function mount(){
        $isAuth = false ;
        if (Auth::user()){
            $isAuth = true;
        }

        $today = date('Y-m-d');

        $this->nachrichten = Nachricht::where(function ($query) use ($today) {
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
        })
        ->where('kopfzeile', true)
        ->get();

    }

    public function render(){

        return view('layouts.header');

    }

    public function nachrichtClick($id){


        $referer = request()->headers->get('referer'); // http://shop.local/login
        $path = parse_url($referer, PHP_URL_PATH); // "/login"
        $page = basename($path);

        if ($page === 'login'){
        //    Log::info('Vor - showNachrichtOnLoginForm dispatch: '.$id);
            $this->dispatch('showNachrichtOnLoginForm', $id);
        }
        else{

            $link = sprintf('/startseite#nachricht%d', $id );
           // Log::info('Link: '.$link);
            return redirect($link);
        }
    }

}
