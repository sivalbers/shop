<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

use App\Models\ApplicationAuth;

class ApiController extends Controller
{
    private $apiKey = '';
    private $apiToken = '';
    private $apiVersion = '';
    private $baseUrl = 'https://netzmaterial-online.de/api.php';


/*
    getTokenHash => den Hashwert des Tokens berechnen und zurückgeben
    getAPIKeyHash => Hashwert des API-Key zurückgeben
    getSessionId => aktuelle Sitzungs-ID zurückgeben
    logRequestHeader => Alle Werte aus dem Header in die Log-Datei schreiben
    buildSessionId => wird gebraucht um API zu prüfen. Gibt die SessionID zurück. ../api/session

*/


    function getTokenHash(string $path, string $key): string{
        return hash_hmac('sha256', $path, $key);
    }

    function getAPIKeyHash(string $key): string{
        return $hashedKey = hash('sha256', $key);
    }

    function getSessionId(){
        return Session::getId(); // Gibt die aktuelle Session-ID zurück
    }

    public function logRequestHeader() {
        $headers = Request::header();
        Log::info(json_encode($headers, JSON_PRETTY_PRINT) );
    }


    public function buildSessionId(){
        $path = '/session';
        $this->logRequestHeader();
        $this->apiKey = Request::header('X-KEY');
        $this->apiToken = Request::header('X-TOKEN');
        $this->apiVersion = Request::header('X-VERSION');

        /* Prüfung ob Header gefüllt sind */
        if (empty($this->apiKey) || empty($this->apiToken) ){
            Log::info(['kein apiKey oder kein apiToken', 'key' => $this->apiKey, 'token' => $this->apiToken ]);
            return $this->rejectRequest();
        }

        /* APIKey suchen */
        $appAuth = ApplicationAuth::get();
        $recAuth = null;
        foreach ($appAuth as $auth){
            if ($this->getAPIKeyHash($auth->apikey) === $this->apiKey){
                $recAuth = $auth;
                break;
            }
        }

        /* Prüfung ob APIKey gefunden wurde */
        if (!$recAuth) {
            // Log::info('apiKey wurde nicht gefunden!');
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        if ($recAuth->status !== 'active') {
            // Log::info('apiKey wurde gefunden, ist aber nicht aktiv!');
            return response()->json(['error' => 'Application is inactive'], 403);
        }
        else{
            Log::info('apiKey wurde gefunden, Session-id wird gesetzt. Ziel erreicht.');
            $tempToken = $this->getTokenHash($path, $recAuth->apikey);
            if ($tempToken === $this->apiToken){
                $recAuth->sessionid = $this->getAPIKeyHash($this->getSessionId());
                $recAuth->sessionexpiry = Carbon::now()->addHours(2);
                $recAuth->lastlogin = Carbon::now();
                $recAuth->save();

                $response = [
                    'VERSION' => '1.7',
                    'request' => [ 'status' => 'success'],
                    'response' => $recAuth->sessionid ];

                return response()->json($response, 200);
            }
        }
    }


    public function checkHeaderToken($path){

        $this->apiKey = Request::header('X-KEY');

        $recAuth = $this->loadAuth($this->apiKey);

        $this->apiToken = Request::header('X-TOKEN'); //HashWert aus Pfad und apiKey

        $rightToken = $this->getTokenHash($path, $recAuth->apikey);
        if ($rightToken === $this->apiToken){


        }



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
    public function loadAuth($apiKey){

        $application = ApplicationAuth::where('apikey', $apiKey)->first();
        if ($application){
            return $application;
        }
        else {
            return null;
        }
    }



    function rejectRequest()
    {
        return response()->json([
            'error' => 'Die Anfrage wurde abgewiesen.',
        ], 403); // 403-Statuscode für "Forbidden"
    }

}
