<?php

namespace App\Livewire;

use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\ApplicationAuth;
use App\Models\ApiSample;
use App\Http\Controllers\ApiController;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;


class ApiTestComponent extends Component
{
    public $id;
    public $appName;
    public $apiKey;
    public $ApplicationAuth;
    public $edSessionId;
    public $sessionexpiry;
    public $status;

    public $apiSamples;
    public $apiSampleId;
    public $apiSampleBezeichnung;
    public $apiSampleHttpMethod;
    public $apiSampleUrl;
    public $apiSampleData;
    public $apiSampleStatus;

    public $testAufruf;
    public $testApiKey;
    public $testApiKeyHash;
    public $testSessionId;
    public $token;
    public $testHttpMethod;

    public $testUrl;
    public $testResult;
    public $testRequest;

    public $statusMessage;

    public $showApiEdit;

    public $showApiSampleEdit;
    public $apiSampleEdit = false ;


    public function mount() {
        $this->showApiEdit = false;
        $this->showApiSampleEdit = false;

        $this->clear();
        $this->update();
    }

    function update(){

        $this->ApplicationAuth = ApplicationAuth::get();
        $this->updateSamples();
    }

    function updateSamples(){
        $this->apiSamples = ApiSample::orderBy('bezeichnung')->get();
    }

    public function render(): View|Closure|string {
        return view('livewire.apitest');
    }

    public function edit($id){

        $api = ApplicationAuth::where('id', $id)->first();
        $this->id = $api->id;
        $this->appName = $api->applicationname;
        $this->apiKey = $api->apikey;

        $this->edSessionId = $api->sessionid;
        $this->sessionexpiry = optional($api->sessionexpiry)->format('Y-m-d\TH:i');
        //$this->sessionexpiry = $api->sessionexpiry ? $api->sessionexpiry->format('Y-m-d\TH:i') : null;

        $this->status = $api->status;
        $this->showApiEdit = true;
    }

    public function neu(){

        $this->clear();
        $this->showApiEdit = true;
    }

    public function save(){
        $api = ApplicationAuth::where('id', $this->id)->first();
        if ($api){
            $api->applicationname = $this->appName;
            $api->apikey = $this->apiKey;
            $api->sessionid = $this->edSessionId;
            $api->sessionexpiry = $this->sessionexpiry;
            $api->status = $this->status;

            $api->save();
        }
        else{
            ApplicationAuth::create([
                'applicationname' => $this->appName,
                'apikey' => $this->apiKey,
                'sessionid' =>  $this->edSessionId,
                'sessionexpiry' =>  $this->sessionexpiry,
                'status' => $this->status,
            ]);
        }
        $this->ApplicationAuth = ApplicationAuth::get();
        $this->clear();
        $this->showApiEdit = false;
    }

    function clear(){
        $this->id = -1;
        $this->appName = null;
        $this->apiKey = null;
        $this->edSessionId = null;
        $this->sessionexpiry = null;
        $this->status = null;

        $this->testAufruf = '/session' ;
        $this->testApiKey = null;
        $this->token = null;

        $this->testUrl = 'https://netzmaterial-online.de/api.php';
        $this->statusMessage = '';
    }

    function generateToken(string $path, string $key): string {

        if ($key && $path){
            return hash_hmac('sha256', $path, $key);
        }
        else
        return 'key-error';
    }

    function generateAPIKey(string $key): string {
        if ($key){
            return $hashedKey = hash('sha256', $key);
        }
        else
        return 'key-error';
    }

    public function btnTestStartNEU(){
        try {
            $this->testSessionId = '';
            $this->statusMessage = 'Abfrage wird gestartet.';
            if (!$this->testResult){
            $this->testResult = 'Neue Abfrage läuft.';
            }
            else{
                $this->testResult = $this->testResult . '<br> neue Abfrage läuft.';
            }

            // Überprüfen, ob die nötigen Werte vorhanden sind
            if (empty($this->testApiKeyHash) || empty($this->token) || empty($this->testUrl)) {
                $this->statusMessage = 'Fehler: API-Key, Token oder URL fehlt.';
                return;
            }

            // Header für den Request
            $headers = [
                'X-KEY' => $this->testApiKeyHash ,
                'X-TOKEN' => $this->token,
                'X-VERSION' => '1.7',
                'X-MODUS' => 'MultiProduct',
            ];

            Log::info(['headers' => $headers] );
            // API-Request mit Laravel HTTP-Client
            $response = Http::withHeaders($headers)->get($this->testUrl . $this->testAufruf);
            Log::info(['response' => $response] );

            // Überprüfung des HTTP-Statuscodes
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['request']['status']) && $data['request']['status'] === 'success') {
                    $this->testResult = $data['response'] ? $this->testResult = json_encode($data, JSON_PRETTY_PRINT) : 'Keine Session-ID in der Antwort gefunden.';
                    $this->testSessionId = $data['response'];
                    $this->statusMessage = 'Abfrage erfolgreich.';
                } else {
                    $this->testResult = json_encode($data, JSON_PRETTY_PRINT);
                    $this->statusMessage = $data['request']['msg'] ?? 'Fehlerhafte Antwort von der API.';
                }
            } else {
                $this->testResult = 'Fehler: HTTP-Statuscode ' . $response->status();
                $this->statusMessage = 'Die Anfrage war nicht erfolgreich.';
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Fehler beim Senden der Anfrage (z. B. Timeout)
            $this->testResult = 'Fehler bei der HTTP-Anfrage: ' . $e->getMessage();
            $this->statusMessage = 'Abfrage fehlgeschlagen.';
        } catch (\Exception $e) {
            // Generische Fehlerbehandlung
            $this->testResult = 'Ein unerwarteter Fehler ist aufgetreten: ' . $e->getMessage();
            $this->statusMessage = 'Abfrage fehlgeschlagen.';
        } finally {
            // Immer ausgeführter Block
            if (empty($this->statusMessage)) {
                $this->statusMessage = 'Abfrage inaktiv.';
            }
        }
        $this->update();
    }

    public function btnGetSessionId(){
        Log::info('ApiTestComponent->btnGetSessionId() - Anfang');
        $this->testSessionId = '';
        if (!$this->testApiKey){
            $this->statusMessage = 'Kein API-Key festgelegt. ';
            return;
        }
        $error = '';

        $this->testSessionId = ApiController::sessionIdAbrufen($this->testApiKey, $this->testUrl, $error);
        Log::info([
            'SessionId' => $this->testSessionId,
            'Error' => $error
            ]);
        if ($error === ''){
            ApiController::saveSessionId($this->testApiKey, $this->testSessionId);
        }

        /*
        API-Key     => wird ohne hash gespeichert, wird mit Hash Übertragen als apiKeyHash
        Session-ID  => wird inkl Hash gespeichert
        */

        if ($this->testSessionId){
            $this->ApplicationAuth = ApplicationAuth::get();
        }
    }

    public function btnTestStart(){
        try {

            Log::info('ApiTestComponent->btnTestStart() - Anfang ***************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');
            Log::info('*********************************************************************************************************************************');

            if (!$this->testSessionId){

                $this->btnGetSessionId();

            }

            if (!$this->testSessionId){

                return;
            }

            $this->statusMessage = 'Abfrage wird gestartet.';

            $this->testApiKeyHash = $this->generateAPIKey($this->testApiKey);
            $this->token = $this->generateToken($this->testAufruf, $this->testApiKey);

            /*
            Log::info([
                'ApiTestComponent->Token Erzeugt ' => $this->token,
                'aufruf' => $this->testAufruf,
                'testApiKey' => $this->testApiKey ] );
            */
            // Überprüfen, ob die nötigen Werte vorhanden sind
            if (empty($this->testApiKeyHash) || empty($this->token) || empty($this->testUrl)) {
                $this->statusMessage = 'Fehler: API-Key, Token oder URL fehlt.';
                return;
            }

            // Header für den Request
            $headers = [
                'X-SESSION' => $this->testSessionId,
                'X-TOKEN' => $this->token,
            ];
            // Log::info(['testSessionId' => $this->testSessionId]);

            $data = json_decode($this->testRequest, true);

            if ($this->testHttpMethod === 'GET') {
                $response = Http::withHeaders($headers)->get($this->testUrl . $this->testAufruf);
            } elseif ($this->testHttpMethod === 'POST') {
                Log::info('POST');
                // Sende die POST-Anfrage mit JSON-Daten im Body
                $response = Http::withHeaders($headers)->post(
                    $this->testUrl . $this->testAufruf,
                    $data // JSON-Daten direkt im Body
                );
            } elseif ($this->testHttpMethod === 'PATCH') {
                // JSON-Daten aus der Testanfrage decodieren
                Log::info('PATCH');

                // Sende die POST-Anfrage mit JSON-Daten im Body
                $response = Http::withHeaders($headers)->patch(
                    $this->testUrl . $this->testAufruf,
                    $data // JSON-Daten direkt im Body
                );
            } elseif ($this->testHttpMethod === 'DELETE') {
                // JSON-Daten aus der Testanfrage decodieren
                Log::info('delete');
                // Sende die POST-Anfrage mit JSON-Daten im Body
                $response = Http::withHeaders($headers)->delete(
                    $this->testUrl . $this->testAufruf );
            }

            // Überprüfung des HTTP-Statuscodes
            if ($response->successful()) {
                $data = $response->json();
                $this->testResult = json_encode($data, JSON_PRETTY_PRINT);
            } else {
                $this->testResult = 'Fehler: HTTP-Statuscode ' . $response->status();
                $this->statusMessage = 'Die Anfrage war nicht erfolgreich.';
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Fehler beim Senden der Anfrage (z. B. Timeout)
            $this->testResult = 'Fehler bei der HTTP-Anfrage: ' . $e->getMessage();
            $this->statusMessage = 'Abfrage fehlgeschlagen.';
        } catch (\Exception $e) {
            // Generische Fehlerbehandlung
            $this->testResult = 'Ein unerwarteter Fehler ist aufgetreten: ' . $e->getMessage();
            $this->statusMessage = 'Abfrage fehlgeschlagen.';
        } finally {
            // Immer ausgeführter Block
            if (empty($this->statusMessage)) {
                $this->statusMessage = 'Abfrage inaktiv.';
            }
        }
        $this->update();
    }

    public function editSample($id){
        $sample = ApiSample::where('id', $id)->first();
        $this->apiSampleId = $sample->id ;
        $this->apiSampleBezeichnung = $sample->bezeichnung ;
        $this->apiSampleHttpMethod = $sample->httpmethod;
        $this->apiSampleUrl = $sample->url ;
        $this->apiSampleData = $sample->data ;
        $this->apiSampleStatus = $sample->status;
        $this->apiSampleEdit = true ;
        $this->showApiSampleEdit = true ;
    }

    public function neuSample(){
        $this->apiSampleId = '';
        $this->apiSampleBezeichnung = '' ;
        $this->apiSampleHttpMethod = 'POST';
        $this->apiSampleUrl = '' ;
        $this->apiSampleData = '' ;
        $this->apiSampleStatus = '';
        $this->apiSampleEdit = false ;
        $this->showApiSampleEdit = true ;

    }

    public function saveSample(){
        if ($this->apiSampleId !== ''){
            $sample = ApiSample::where('id', $this->apiSampleId)->first();
            $sample->bezeichnung = $this->apiSampleBezeichnung;
            $sample->httpmethod = $this->apiSampleHttpMethod;
            $sample->url = $this->apiSampleUrl;
            $sample->data = $this->apiSampleData;
            $sample->status = $this->apiSampleStatus;
            $sample->save();
        }
        else{
            ApiSample::create([
                'bezeichnung' => $this->apiSampleBezeichnung,
                'httpmethod' => $this->apiSampleHttpMethod,
                'url' => $this->apiSampleUrl,
                'data' => $this->apiSampleData,
                'status' => $this->apiSampleStatus,
            ]);
        }
        $this->showApiSampleEdit = false ;
        $this->updateSamples();
    }

    public function deleteSample(){
        $sample = ApiSample::where('id', $this->apiSampleId)->first();
        $sample->delete();
        $this->showApiSampleEdit = false ;
        $this->updateSamples();
    }

    public function btnBuildToken(){

        $this->token = $this->generateToken($this->testAufruf, $this->testApiKey);
        // Log::info([ 'BuildToken', 'Aufruf' => $this->testAufruf, 'apiKey' => $this->testApiKey, 'token' => $this->token]);
        $this->testApiKeyHash = $this->generateAPIKey($this->testApiKey);
    }

    public function setTestApiKey($value){

        $this->testApiKey = $value;
    }

    public function setApplikationsnameAsUrl($value){
        $this->testUrl = $value;
    }

    public function setTestSessionId($value){
        $this->testSessionId = $value;
    }

    public function setTestUrl($value){
        $this->testUrl = $value;
    }

    public function btnSetTestAufruf($id){
        $sample = ApiSample::findOrFail($id);
        $this->testHttpMethod = $sample->httpmethod;
        $this->testAufruf = $sample->url;
        $this->testRequest = $sample->data;
        $this->testResult = '';
    }
}
