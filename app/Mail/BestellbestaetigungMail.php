<?php

namespace App\Mail;

use App\Models\BestellungPos;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Headers;

use Symfony\Component\Process\Process;


use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class BestellbestaetigungMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $details;

    public function __construct($details){
        $this->details = $details;

        $this->loadBestellung();
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe' => '<mailto:unsubscribe@netzmaterialonline.de>, <https://netzmaterialonline.de/unsubscribe>',
            ],
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $mail = Auth()->user()->email;
        return new Envelope(
            subject: 'Bestellung Nr.: '.$this->details['bestellung']->nr,
            to: [  'mail@andreasalbers.de', 'andreas.albers@sieverding.de'],
        );
    }

    public function testMailRendering()
    {
        $htmlContent = View::make('emails.BestellbestaetigungMail', [
            'order' => $this->details,
        ])->render();

    }

    /**
     * Get the message content definition.
     */
    public function build()
    {

        return $this->view('emails.BestellbestaetigungMailHtml')
                    ->text('emails.bestellbestaetigungMailPlaintext')
                    ->with([
                        'order' => $this->details,
                    ])
                    ->subject('Bestellbestätigung'); // Setzen Sie hier den gewünschten Betreff



    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }


    public function loadBestellung(){
        $this->details['bestellungPos'] = BestellungPos::where('bestellnr', $this->details['bestellung']->nr)->where('menge', '<>', 0)->orderBy('sort')->get();

    }


}
