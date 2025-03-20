<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Bestellung;
use App\Models\Position;
use App\Models\Nachricht;

use App\Helpers\AuthHelper;
use Livewire\Attributes\On;


new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public bool $showForm = false;
    public bool $isModified = false ;
    public $nachricht;

    #[On('showNachrichtOnLoginForm')]
    public function showNachricht($id)
    {
        Log::info('showNachricht ausgelöst mit ID: ' . $id);
        $this->nachricht = Nachricht::where('id', $id)->first();
        $this->showForm = true;
    }

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        if (Auth::user()) {
            $user_debitor = Auth::user()->standardDebitor();
            AuthHelper::AfterLogin($user_debitor);
        }
        $this->redirectIntended(default: route('startseite', absolute: false), navigate: true);
    }
};
 ?>

<div class="" x-data="{ showForm: @entangle('showForm'), }"
        x-on:click.self="showForm = false "
        x-on:keydown.escape.window="showForm = false">

    <div class="pb-5">Sie sind derzeit nicht angemeldet.</div><br>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('auth.email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="text" name="email" required autofocus autocomplete="email" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('auth.Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('auth.Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('auth.Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('auth.Log in') }}
            </x-primary-button>
        </div>

    </form>

    <x-my-form class="max-h-[70vH] overflow-y-scroll">
        @if ($nachricht)
        <div class="flex flex-col">
            <div class="flex flex-row "  id="nachricht{{ $nachricht->id }}">
                <div class="min-w-12 flex justify-center">
                    @if ($nachricht->prioritaet === 'hoch')
                        <span class="text-red-600"><x-fluentui-important-24 class="h-12" /></span>
                    @elseif ($nachricht->prioritaet === 'mittel')
                        <span class="text-ewe-gruen"><x-fluentui-important-24-o class="h-12" /></span>
                    @endif
                </div>

                <div class="flex flex-col w-full border-b border-sky-600">
                    <div class="flex flex-row items-center justify-between border-b border-sky-600">
                        <div class="text-xl text-sky-600 font-bold">
                            {{ $nachricht->kurztext }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $nachricht->updated_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="text-base max-h-[55vH] overflow-y-auto">
                        {!! nl2br(e($nachricht->langtext)) !!}
                    </div>

                    @if (!empty($nachricht->links))
                        <div class="text-base pt-2">
                            @php
                                $links = $nachricht->getLinksArray();
                            @endphp
                            @if (count($links) > 1)
                                Links:
                                @foreach ($links as $link)
                                    <div class="flex flex-col">
                                        <div class="flex flex-row">
                                            <div class="min-w-4"></div>
                                            <div>
                                                <span class="text-ewe-gruen">
                                                    <a href="{{ $link['link'] }}" target="_blank">{{ $link['beschreibung'] }}</a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                Link: <span class="text-ewe-gruen">
                                    <a href="{{ $links[0]['link'] }}" target="_blank">{{ $links[0]['beschreibung'] }}</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-right  py-2">
                <button wire:click="showForm = false"
                    class="inline-flex items-center px-4 py-2 bg-sky-600 dark:bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Schließen
                </button>

            </div>
        </div>

        @endif

    </x-my-form>


</div>
