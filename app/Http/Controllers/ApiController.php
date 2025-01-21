<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

use App\Models\ApplicationAuth;

class ApiController extends Controller
{
    private $apiKey = '13ac184b-34e0-edf7-f5aa-fcbba0704853';
    private $baseUrl = 'https://netzmaterial-online.de/api.php';

    /**
     * Erstellt den HMAC-SHA256-Token basierend auf dem Pfad und dem API-Key.
     *
     * @param string $path
     * @return string
     */


    // erzeugt einen Token aus dem Pfad und dem apiKey
    function generateToken(string $path, string $key): string
    {
        return hash_hmac('sha256', $path, $key);
    }

    public function getSessionOSGWebshop() {

        $headers = Request::header();
        return json_encode($headers, JSON_PRETTY_PRINT) ;

    }

    /* erzeugt eine sessionID */
    public function getSessionId($path)
    {

        $this->apiKey = Request::header('X-KEY');

        // Lädt den Datensatz mit dem apiKey => der Anwendung, die registriert ist. z.B. EPR-System
        $application = $this->loadApiKey($this->apiKey);
        if (empty($application)){
            return false;
        }


        $token = $this->generateToken($path, $this->apiKey ); // Token erstellen

        if ($token === Request::header('X-KEY')){

        }


    }


    public function createApiKey($appName){
        ApplicationAuth::create([
            'applicationname' => $appName,
            'apikey' => Str::random(64), // Ein sicherer, eindeutiger API-Key
            'status' => 'inactive',
            'allowedendpoints' => json_encode(['/cart', '/order']),
        ]);
    }

    /*

    */
    public function checkApiKeyURL($url){

    }

    public function checkApiKey(){

        $apiKey = Request::header('X-KEY');

        $application = ApplicationAuth::where('apikey', $apiKey)->first();

        if (!$application) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        if ($application->status !== 'active') {

            return response()->json(['error' => 'Application is inactive'], 403);
        }
        else {
            if ($application->status === 'inactive') {
                $application->status = 'active';
            }

            $application->sessionid = session()->getId(); // Laravel-Session-ID übernehmen
            $application->sessionexpiry = now()->addHours(2); // Ablaufzeit festlegen
            $application->lastlogin = now();
            $application->save();
        }
    }

    // Es wird nur geprüft, ob X-KEY als API-Key in der Datenbank gespeichert ist.
    public function loadApiKey($apiKey){

        $application = ApplicationAuth::where('apikey', $apiKey)->first();
        if ($application){
            return $application;
        }
        else {
            return null;
        }
    }

}
