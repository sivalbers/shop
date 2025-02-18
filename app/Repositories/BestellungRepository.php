<?php

namespace App\Repositories;

use App\Models\Bestellung;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\BestellungToXMLService;

class BestellungRepository
{
    private string $apiUrl = "https://veo-services.gws-cloud.de/osg/v1/orders";
    private string $customerId = "b3025cc0-c234-4b69-8b04-a67ef013faf3";
    private string $subscriptionKey = "cd8a3a82b46446b9a8dfb5adf2589211";

    public function sendToERP(Bestellung $bestellung)
    {
        try {

            // Bestellung in XML umwandeln
            $xmlString = (new BestellungToXMLService($bestellung))->getXML();
            Log::info(['XML' => $xmlString ]);
            // API-Request mit Laravel HTTP-Client
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
                'customer-Id' => $this->customerId,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])->withBody($xmlString, 'application/xml')->post($this->apiUrl, $xmlString);


            $apiLog = null;

            try{
                $apiLog = ApiLog::create([

                'httpmethod' => 'POST',
                'pfad' => $this->apiUrl,
                'key' => $this->subscriptionKey,
                'session' => '',
                'token' => $this->customerId,
                'data' => $xmlString,
                ]);

            } catch (\Exception $e) {
                Log::error([ 'Exception ist aufgetreten: ' => $e->getMessage()] );
            } finally {
            }



            $apiLog->update([
                'response' => json_encode($response->body()),
            ]);

            // Erfolgreiche Antwort prüfen
            if ($response->successful()) {
                Log::info(['success' => true, 'data' => $response->body()]);

                $bestellung->erpid = $this->getEntityId($response->body());
                $bestellung->save();
                return ['success' => true, 'data' => $response->body()];
            }


            // Fehlerbehandlung
            throw new Exception("ERP API Fehler: " . $response->body());
        } catch (Exception $e) {
            throw new Exception("Fehler beim Senden der Bestellung: " . $e->getMessage());
        }
    }

    public function getEntityId($jsonString)
    {
        $data = json_decode($jsonString, true);
        return $data['entityId'] ?? 'Kein Wert';
    }
}
