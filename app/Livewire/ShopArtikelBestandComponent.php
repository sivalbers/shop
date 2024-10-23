<?php

namespace App\Livewire;

use Livewire\Component;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;


class ShopArtikelBestandComponent extends Component
{
    public $bestand;

    public function mount($artikelnr)
    {

        $url = env('URL_ARTIKELBESTAND');

        $url = str_replace('XXXX', $artikelnr, $url);
        $username = 'testuser';
        $password = 'Sieverding22!';

        // Initialisiere den Guzzle-Client
        $client = new Client();

        try {
            Log::info("Start: ", [$artikelnr, $url]);
            // Sende die Anfrage an den OData-Dienst mit Basic-Authentifizierung
            $response = $client->request('GET', $url, [
                'auth' => [$username, $password],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            // Hole den Body der Antwort
            $data = json_decode($response->getBody()->getContents(), true);
            $this->bestand = $data['value'][0];
            Log::info("Ende: ", [$artikelnr]);

        } catch (\Exception $e) {
            return "Fehler"; //response()->json(['error' => 'Fehler beim Import: ' . $e->getMessage()], 500);
        }
    }



    public function render()
    {




        return view('livewire.shop.shopartikelbestand');
    }
}
