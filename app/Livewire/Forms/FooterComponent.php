<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\Form;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;


class FooterComponent extends Component
{

    public string $header = '';

    public function render(){

        return view('layouts.footer');

    }

}
