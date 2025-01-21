<?php

namespace App\Livewire;

use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\ApplicationAuth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;


class ApiTestComponent extends Component
{
    public $id;
    public $appName;
    public $apiKey;
    public $ApplicationAuth;
    public $sessionId;
    public $sessionexpiry;
    public $status;

    public $aufruf;
    public $testApiKey;
    public $testApiKeyHash;
    public $token;

    public $testUrl;
    public $testResult;

    public $statusMessage;

    /**
     * Create a new component instance.
     */
    public function mount()
    {   $this->clear();
        $this->ApplicationAuth = ApplicationAuth::get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('livewire.apitest');
    }

    public function edit($id){
        $api = ApplicationAuth::where('id', $id)->first();
        $this->id = $api->id;
        $this->appName = $api->applicationname;
        $this->apiKey = $api->apikey;

        $this->sessionId = $api->sessionid;
        $this->sessionexpiry = optional($api->sessionexpiry)->format('Y-m-d\TH:i');
        //$this->sessionexpiry = $api->sessionexpiry ? $api->sessionexpiry->format('Y-m-d\TH:i') : null;


        $this->status = $api->status;
    }

    public function neu(){

        $this->clear();
    }

    public function save(){
        $api = ApplicationAuth::where('id', $this->id)->first();
        if ($api){
            $api->applicationname = $this->appName;
            $api->apikey = $this->apiKey;
            $api->sessionid = $this->sessionId;
            $api->sessionexpiry = $this->sessionexpiry;
            $api->status = $this->status;

            $api->save();
        }
        else{
            ApplicationAuth::create([
                'applicationname' => $this->appName,
                'apikey' => $this->apiKey,
                'sessionid' =>  $this->sessionId,
                'sessionexpiry' =>  $this->sessionexpiry,
                'status' => $this->status,
            ]);
        }
        $this->ApplicationAuth = ApplicationAuth::get();
        $this->clear();
    }

    function clear(){
        $this->id = -1;
        $this->appName = null;
        $this->apiKey = null;
        $this->sessionId = null;
        $this->sessionexpiry = null;
        $this->status = null;

        $this->aufruf = '/session' ;
        $this->testApiKey = null;
        $this->token = null;

        $this->testUrl = 'https://netzmaterial-online.de/api.php';
        $this->statusMessage = '';
    }

    public function buildToken(){
        $this->testApiKeyHash = $this->generateAPIKey($this->testApiKey);
        $this->token = $this->generateToken($this->aufruf, $this->testApiKey );
    }

    function generateToken(string $path, string $key): string
    {
        return hash_hmac('sha256', $path, $key);
    }

    function generateAPIKey(string $key): string
    {
        // return hash_hmac('sha256', $key, '');
        return $hashedKey = hash('sha256', $key);
    }

    public function funcTestUrl()
    {
        try {
            $this->statusMessage = 'Abfrage wird gestartet.';

            // Überprüfen, ob die nötigen Werte vorhanden sind
            if (empty($this->testApiKey) || empty($this->token) || empty($this->testUrl)) {
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

            Log::info($headers);
            // API-Request mit Laravel HTTP-Client
            $response = Http::withHeaders($headers)->get($this->testUrl . $this->aufruf);

            // Überprüfung des HTTP-Statuscodes
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['request']['status']) && $data['request']['status'] === 'success') {
                    $this->testResult = $data['session_id'] ?? 'Keine Session-ID in der Antwort gefunden.';
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
    }

    public function setTestApiKey($value){

        $this->testApiKey = $value;
    }

    public function setApplikationsnameAsUrl($value){
        $this->testUrl = $value;
    }


}
