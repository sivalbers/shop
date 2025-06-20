<?php

use App\Models\User;
use App\Models\Debitor;
use App\Models\UserDebitor;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $inputName = '';
    public string $inputEmail = '';

    public $users = [];
    public $debitor;
    public $rolle = 0;
    public $showAbfrage = false;
    public $showForm = false;
    public $isModified = false;
    public $abfrageEMail;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->inputName = Auth::user()->name;
        $this->inputEmail = Auth::user()->email;

        $aktDebitor = session()->get('debitornr');

        $ich = UserDebitor::where('email', Auth::user()->email)
            ->where('debitor_nr', $aktDebitor)
            ->first();
        $this->rolle = $ich->rolle;

        $this->debitor = Debitor::find($aktDebitor);

        $this->updateUsers();
    }

    private function updateUsers()
    {
        $this->users = UserDebitor::where('debitor_nr', session()->get('debitornr'))->get();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function AddUser(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'inputName' => ['required', 'string', 'max:255'],
            'inputEmail' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('startseite', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function btnDelete($email)
    {
        $this->showAbfrage = true;
        $this->abfrageEMail = $email;
    }

    public function doDelete($email)
    {
        $this->showAbfrage = false;

        $userDebitor = UserDebitor::where('email', $email)
            ->where('debitor_nr', session()->get('debitornr'))
            ->delete();

        $this->updateUsers();

        session()->flash('message', "Benutzer '$email' wurde entfernt.");

        $this->abfrageEMail = '';
    }

    public function cancelDelete()
    {
        $this->showAbfrage = false;
        $this->abfrageEMail = '';
    }

    public function neuerBenutzer()
    {
        $this->inputName = '';
        $this->inputEmail = '';
        $this->showForm = true;
    }

    public function createUserDebitor() {
        if (empty($this->inputName) || empty($this->inputEmail)) {
            session()->flash('message', "Name oder E-Mail-Adresse fehlt.");
            return;
        }

        $user = User::where('email', $this->inputEmail)->first();

        if (!$user) {
            $user = User::create([
                'name' => $this->inputName,
                'email' => $this->inputEmail
            ]);
        } else {
            $user->name = $this->inputName;
            $user->save();
        }

        UserDebitor::create([
            'email' => $this->inputEmail,
            'debitor_nr' => session()->get('debitornr'),
            'rolle' => 0
        ]);

        $this->updateUsers();

        $this->dispatch('neuerBenutzer', name: $this->inputName);

        $this->showForm = false;
    }

}; ?>

<div x-data="{ showAbfrage: @entangle('showAbfrage'), showForm: @entangle('showForm') }"
     x-on:click.self="showAbfrage = false, showForm = false"
     x-on:keydown.escape.window="showAbfrage = false, showForm = false">

    <section class="w-full">
        <header>
            <div class="flex flex-row items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Benutzerliste') }} für Debitor: "{{ $debitor->nr }} {{ $debitor->name }}"
                    </h2>
                </div>
                <div>
                    @if (session()->has('message'))
                        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                            class="text-red-500 alert alert-success transition-opacity duration-500 ml-56 border border-red-600 bg-red-500 rounded px-6 text-white">
                            {{ session('message') }}
                        </div>
                    @endif
                    <x-action-message class="me-3 border border-red-600 bg-red-500 rounded px-6 text-white" on="neuerBenutzer">
                        {{ __('Änderung wurde gespeichert.') }}
                    </x-action-message>
                </div>
            </div>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            </p>
        </header>

        <div class="flex flex-col text-sm">
            <div class="flex flex-row border-b items-end gap-4">
                <div class="w-1/12">
                    Rolle
                </div>
                <div class="w-5/12">
                    Name
                </div>
                <div class="w-5/12">
                    E-Mail-Adresse
                </div>
                <div class="w-1/12 text-center">
                    @if ($rolle === 1)
                        Benutzer löschen
                    @endif
                </div>
            </div>
            @foreach ($users as $iUser)
                <div class="flex flex-row items-center gap-4 hover:bg-ewe-ltgruen">
                    <div class="w-1/12">
                        @if ($iUser->rolle === 1)
                            <x-fluentui-key-16-o class="h-5 w-6" title="Hauptbenutzer" />
                        @else
                        @endif
                    </div>
                    <div class="w-5/12">
                        {{ $iUser->user->name }} @if ($iUser->user->email === Auth::user()->email)
                            (angemeldet)
                        @endif
                    </div>
                    <div class="w-5/12">
                        {{ $iUser->user->email }}
                    </div>
                    <div class="w-1/12 text-center">
                        @if ($iUser->user->email != Auth::user()->email)
                            @if ($rolle === 1)
                                <button wire:click="btnDelete('{{ $iUser->user->email }}')"
                                    class=" border border-red-400 text-white rounded-md px-2 w-10"
                                    title="Nach Rückfrage wird der Zugang von '{{ $iUser->user->email }}' für diesen Debitor gelöscht.">
                                    <x-fluentui-shopping-bag-dismiss-20-o class="h-5 text-red-500" />
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
            @if ($rolle === 1)
            <div class="flex flex-row border-t items-end gap-4 justify-end">
                <button wire:click="neuerBenutzer" class="mt-2 border border-sky-600 bg-sky-600 text-white rounded shadow-md px-4">Benutzer erstellen</button>
            </div>
            @endif
        </div>


            <x-my-form class="">
                <form wire:submit="createUserDebitor" class="mt-6 space-y-6"> <!-- updateProfileInformation -->
                    <div class="flex flex-row items-end space-x-4">
                        <div class="w-3/6">
                            <x-input-label for="inputName" :value="__('Name')" />
                            <x-text-input wire:model="inputName" id="inputName" name="inputName" type="text"
                                class="mt-1 block w-full" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="w-2/6">
                            <x-input-label for="inputEmail" :value="__('E-Mail-Adresse')" />
                            <x-text-input wire:model="inputEmail" id="inputEmail" name="inputEmail" type="email"
                                class="mt-1 block w-full" required autocomplete="email" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                                <div>
                                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                        {{ __('Your email address is unverified.') }}

                                        <button wire:click.prevent="sendVerification"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                            {{ __('A new verification link has been sent to your email address.') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="w-1/6 flex items-center gap-4 pb-1">
                            <x-primary-button>{{ __('Speichern') }}</x-primary-button>

                            <x-action-message class="me-3" on="profile-updated">
                                {{ __('Saved.') }}
                            </x-action-message>
                        </div>
                    </div>
                </form>
            </x-my-form>


    </section>

    <div class="z-50 flex items-center justify-center fixed inset-0 item-center w-full h-full bg-slate-100/60 backdrop-blur-[2px]"
        x-show="showAbfrage" x-cloak x-on:click.self="showAbfrage = false, showForm = false"
        x-on:keydown.escape.window="showAbfrage = false, showForm = false">
        <form class="border border-sky-600 rounded-md shadow-md bg-slate-100 p-8">
            <div class="m-2 center">
                Möchten Sie den Benutzer <span class="font-bold">"{{ $abfrageEMail }}"</span> wirklich löschen?
                <button wire:click="doDelete('{{ $abfrageEMail }}')"
                    class="ml-4 border border-sky-600 rounded shadow-md bg-ewe-ltgruen text-black w-32"> Ja </button>
                <button wire:click="cancelDelete"
                    class="border border-sky-600 rounded shadow-md bg-sky-300  text-black w-32"> Nein </button>
            </div>
        </form>
    </div>
</div>
