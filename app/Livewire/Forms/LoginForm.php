<?php

namespace App\Livewire\Forms;


use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\Nachricht;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;
    public bool $showForm = false ;
    public bool $isModified = false ;
    public $nachricht;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        try {
            // Prüfe, ob die Authentifizierung funktioniert
            if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'form.email' => trans('auth.failed'),
                ]);
            }
        } catch (\RuntimeException $exception) {
            // Fehler beim Passwort (nicht bcrypt)
            if (str_contains($exception->getMessage(), 'does not use the Bcrypt algorithm')) {
                throw ValidationException::withMessages([
                    'form.password' => trans('auth.password_format_error'), // Neue Übersetzung für diesen Fehler anlegen
                ]);
            }

            // Allgemeiner Fehler
            throw ValidationException::withMessages([
                'form.general' => trans('auth.general_error'), // Übersetzung für allgemeinen Fehler
            ]);
        }

        // RateLimiter zurücksetzen, falls erfolgreich
        RateLimiter::clear($this->throttleKey());
    }
    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            // 'form.email' => trans('auth.throttle', [
            'form.login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }


}
