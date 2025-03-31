<?php

namespace App\Repositories;


use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BelegarchivRepository
{
    private string $apiUrl = "https://veo-services.gws-cloud.de/osg/v1/documents";
    private string $customerId = "b3025cc0-c234-4b69-8b04-a67ef013faf3";
    private string $subscriptionKey = "cd8a3a82b46446b9a8dfb5adf2589211";

    public function readBelegArchiv($dateVon, $dateBis )
    {
        try {
            $debitorNr = session()->get('debitornr');

            if (!$debitorNr) {
                throw new \Exception("Debitorennummer nicht gefunden in der Session.");
            }

            $jsonArray = [
                "documentType" => 0,
                "user" => $debitorNr,
                "sender" => "MULTISHOP",
                "startDate" => date_format($dateVon, 'Y-m-d'),
                "endDate" => date_format($dateBis, 'Y-m-d'),
            ];

            $json = json_encode($jsonArray);


            Log::info(['Belege im JSON-Format' => $json ]);
            // API-Request mit Laravel HTTP-Client
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'customer-Id' => $this->customerId,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])->withBody($json, 'application/xml')->post($this->apiUrl, $json);


            $apiLog = null;

            try{
                $apiLog = ApiLog::create([

                'httpmethod' => 'POST',
                'pfad' => $this->apiUrl,
                'key' => $this->subscriptionKey,
                'session' => '',
                'token' => $this->customerId,
                'data' => $json,
                ]);

            }
            catch (\Exception $e) {
                Log::error([ 'Exception ist aufgetreten: ' => $e->getMessage()] );
                }
            finally {}


            $apiLog->update([
                'response' => json_encode($response->body()),
            ]);


            // Erfolgreiche Antwort prüfen
            if ($response->successful()) {
                Log::info(['success' => true, 'data' => $response->body()]);

                return ['success' => true, 'data' => $response->body()];
            }
            else {

                Log::info('Fehler Belege wurden nicht empfangen.');

                throw new Exception("ERP API Fehler: " . $response->body());
            }
        } catch (Exception $e) {
            throw new Exception("Fehler beim holen der Belege Senden der Bestellung: " . $e->getMessage());
        }
    }

    public function getEntityId($jsonString)
    {
        $data = json_decode($jsonString, true);
        return $data['entityId'] ?? 'Kein Wert';
    }
}
