<?php

namespace App\Mail;

use App\Models\BestellungPos;
use App\Models\Config;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $bcc = Config::globalString('mail-bcc');
        $bccArr = explode(';', $bcc);

        $cc = Config::globalString('mail-cc');
        $ccArr = explode(';', $cc);


        $empfaenger = [ $mail ];
        Log::info(['empfänger' => $empfaenger]);
        Log::info(['cc' => $ccArr]);
        Log::info(['bcc' => $bccArr]);


        if (!empty( $this->details['bestellung']->kundenbestellnr)){
            $subject = sprintf('Bestellbestätigung für Ihre Bestellung Nr.: %s - [ Kundenbestellnr.: %s ]',
                                $this->details['bestellung']->nr,
                                $this->details['bestellung']->kundenbestellnr
                );
        }
        else
            $subject = sprintf('Bestellbestätigung für Ihre Bestellung Nr.: %s', $this->details['bestellung']->nr );

        return new Envelope(
            subject: $subject,
            to: $empfaenger,
            bcc: $bccArr,
            cc: $ccArr,
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

        return $this->view('emails.bestellbestaetigungMailHtml')
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
