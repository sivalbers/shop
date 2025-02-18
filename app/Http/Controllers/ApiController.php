<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Http;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

use App\Models\ApplicationAuth;
use App\Services\ApiService;

class ApiController extends Controller
{

    protected $apiService;

    private $apiKey = '';
    private $apiToken = '';
    private $apiVersion = '';

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }


/*
    getTokenHash => den Hashwert des Tokens berechnen und zurückgeben
    getAPIKeyHash => Hashwert des API-Key zurückgeben
    getSessionId => aktuelle Sitzungs-ID zurückgeben
    logRequestHeader => Alle Werte aus dem Header in die Log-Datei schreiben
    buildSessionId => wird gebraucht um API zu prüfen. Gibt die SessionID zurück. ../api/session

*/


    static function  getTokenHash(string $path, string $key): string{
        return hash_hmac('sha256', $path, $key);
    }

    static function getAPIKeyHash(string $key): string{
        return $hashedKey = hash('sha256', $key);
    }

    function getSessionId(){
        return Session::getId(); // Gibt die aktuelle Session-ID zurück
    }

    public function logRequestHeader(Request $request) {
        $headers = $request->header();
        Log::info([ 'ApiController->logRequestHeader()', 'header' => json_encode($headers, JSON_PRETTY_PRINT) ]);
    }


    public function verarbeiteApiUrlGet(Request $request, $url, $id = null ){
        // $this->logRequestHeader($request);
        if ($url === 'session'){
            return $this->buildSessionId($request);
        }
        else
        {
            if (!$this->checkSession($request, $url, $id)){
                return response(['response' => 'Get-Session-Error'], 401);
            }
        }

        return response()->json($this->apiService->handleGetRequest($url, $request, $id));

        Log::info([ 'ApiController->verarbeiteApiUrlGet() Anfrage an den Shop an ', 'url' => $url] );

        if ($url === 'categories'){
            $response = [
                'VERSION' => '1.7',
                'request' => [ 'status' => 'success'],
                'response' => 'OK' ];

            return response()->json($response, 200);
        }
    }

    public function verarbeiteApiUrlPost(Request $request, $url, $id = null ){

        if (!$this->checkSession($request, $url, $id)){
            return response(['response' => 'Post-Session-Error'], 401);
        }

        return response()->json($this->apiService->handlePostRequest($url, $request, $id));

    }

    public function verarbeiteApiUrlPatch(Request $request, $url, $id = null ){

        if (!$this->checkSession($request, $url, $id)){
            return response(['response' => 'Patch-Session-Error'], 401);
        }

        return response()->json($this->apiService->handlePatchRequest($url, $request, $id));

    }

    public function verarbeiteApiUrlDelete(Request $request, $url, $id = null ){
        if (!$this->checkSession($request, $url, $id)){
            return response(['response' => 'Delete-Session-Error'], 401);
        }
        return response()->json($this->apiService->handleDeleteRequest($url, $request, $id));
    }

    public function checkSession($request, $url, $id = null){

        // Log::info(["checkSession($url, $id)" ]);

        $okay = false ;

        $xSession = $request->header('X-SESSION');

        $auth = ApplicationAuth::where('sessionid',  $xSession)->first();

        // Log::info([ 'x-session' => $xSession, '$auth-Found' => $auth->apikey, 'expiers ' => $auth->sessionexpiry->format('h:m:s'),  Carbon::now()->format('h:m:s')]);

        if ($auth && Carbon::parse($auth->sessionexpiry)->isAfter(Carbon::now())) {
            $xToken = $request->header('x-token');
            $uu = '/'.$url;
            if ($id){
                $uu = $uu .'/'. $id;
            }
            // Log::info(['url' => $uu ] );
            $bToken = $this->getTokenHash($uu, $auth->apikey);
            // Log::info(['xToken === bToken', 'xToken' => $xToken, 'bToken' => $bToken, 'apiKey' => $auth->apikey ]);
            if ( $bToken === $xToken){
                // Log::info('checkSession = true');
                $okay = true;
            }
            // else
                // Log::info('checkSession = false');

        }
        else {
            // Log::info('SessionId = Session gefunden, Auth aus DB gelesen.');
        }

        return $okay;
    }

    /*

        Es wird die Anfrage /session mit dieser Funktion beantwortet.
        Eine Fremde Anwendung sendet eine Anfrage. Wenn der api-Key stimmt, wird eine Session-ID
        in die Datenbank geschrieben und zurückgegeben.
    */

    public function buildSessionId(Request $request){
        // Log::info('ApiController->buildSessionId() - Anfang');
        $path = '/session';

        try{
            $this->apiKey = $request->header('X-KEY');
            $this->apiToken = $request->header('X-TOKEN');
            $this->apiVersion = $request->header('X-VERSION');
        } catch (\Illuminate\Http\Client\RequestException $e) {
        //    Log::error($e->getMessage());
        } catch (\Exception $e) {
        //    Log::error($e->getMessage());
        } finally {
        //    Log::info('Fehlerfrei');
        }
        /*
        Log::info([
            'apiKey' => $this->apiKey,
            'apiToken' => $this->apiToken,
            'apiVersion' => $this->apiVersion ]);
        */
        /* Prüfung ob Header gefüllt sind */
        if (empty($this->apiKey) || empty($this->apiToken) ){
            //Log::info(['kein apiKey oder kein apiToken', 'key' => $this->apiKey, 'token' => $this->apiToken ]);
            return response()->json([
                'error' => 'Die Anfrage wurde abgewiesen.',
            ], 403);
        }

        /* APIKey suchen */
        $appAuth = ApplicationAuth::get();
        $recAuth = null;

        foreach ($appAuth as $auth){
            //Log::info([ 'DB-id' => $auth->id, 'DB apiKeyHash' => $this->getAPIKeyHash($auth->apikey), '== apiKey' => $this->apiKey ]);
            if ($this->getAPIKeyHash($auth->apikey) === $this->apiKey){
                $recAuth = $auth;
                //Log::info('Gefunden.');

                break;
            }
        }
        // Log::info('nach foreach');

        /* Prüfung ob APIKey gefunden wurde */
        if (!$recAuth) {
            // Log::info('apiKey wurde nicht gefunden!');
            return response()->json(['error' => 'Invalid API Key'], 401);
        }
        // Log::info(['status' => $recAuth->status, 'active'] );
        if ($recAuth->status !== 'active') {
            Log::info('apiKey wurde gefunden, ist aber nicht aktiv!');
            return response()->json(['error' => 'Application is inactive'], 403);
        }
        else{
            // Log::info('apiKey wurde gefunden, Session-id wird gesetzt. Ziel erreicht.');
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
                // Log::info(['Response von buildSessionId() ' => $response]);
                return response()->json($response, 200);
            }
        }
    }


    public static  function sessionIdAbrufen($apiKey, $url, &$error){
        try {
            $error = '' ;
            $result = '';
            $path = '/session';
            $apiKeyHash = ApiController::getAPIKeyHash($apiKey);

            $headers = [
                'X-KEY' => $apiKeyHash,
                'X-TOKEN' => ApiController::getTokenHash($path, $apiKey),
                'X-VERSION' => '1.7',
                'X-MODUS' => 'MultiProduct',
            ];

            $response = Http::withHeaders($headers)->get($url . $path);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['request']['status']) && $data['request']['status'] === 'success') {

                    $result = $data['response'];
                }
            }

        } catch (\Illuminate\Http\Client\RequestException $e) {
            $error = 'Fehler: '. $e->getMessage();
        } catch (\Exception $e) {

            $error = 'Ein unerwarteter Fehler ist aufgetreten: ' . $e->getMessage();

        } finally {
            return $result;
        }
    }


    public static function saveSessionId($apiKey, $sessionId){
        $api = ApplicationAuth::where('apikey', $apiKey)->first();
        if ($api){
            $api->sessionid = $sessionId;
            $api->sessionexpiry = Carbon::now()->addHours(2);
            $api->lastlogin = Carbon::now();
            $api->save();
        }
    }

}
