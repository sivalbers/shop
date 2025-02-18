<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CustomResetPassword extends Notification
{
    use Queueable;

    protected $token;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token; // Speichere das Token
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Passwort zurücksetzen für Ihr Konto')
                    ->greeting(__('Hallo, lieber Kunde!'))
                    ->line('Sie haben eine Anfrage zum Zurücksetzen des Passworts gestellt.')
                    ->action(__('Passwort zurücksetzen'), $this->resetUrl($notifiable))
                    ->line(__('Wenn Sie diese Anfrage nicht gestellt haben, ignorieren Sie diese E-Mail einfach.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    protected function resetUrl(object $notifiable): string
    {
        return URL::route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
