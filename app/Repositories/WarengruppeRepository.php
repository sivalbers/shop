<?php

namespace App\Repositories;

use App\Models\Warengruppe;
use App\Models\WgHelper;
use App\Models\Config;

use Illuminate\Support\Facades\Log;

class WarengruppeRepository
{

    private string $logLevel;
    private WgHelper $wgh;
    private Warengruppe $wg;

// #region Logging
    public function __construct(){
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)

        $this->logLevel = Config::globalString('logging.warengruppe_repository_log_level', 'debug');

        Log::info(['logging.warengruppe_repository_log_level' => $this->logLevel]);
    }

    public function setLogLevel(string $level): void{
        $this->logLevel = $level;
    }

    private function shouldLog(string $level): bool{
        $allowedLogLevels = [
            'debug'   => 0,
            'info'    => 1,
            'warning' => 2,
            'error'   => 3,
        ];

        return $allowedLogLevels[$level] >= $allowedLogLevels[$this->logLevel];
    }

    private function logMessage(string $level, string $message, array $context = []): void{
        if ($this->shouldLog($level)) {
            Log::$level($message, $context);
        }
    }
// #endregion



    public function getByCode($code)
    {
        Log::info('getByCode()');
        try{
            return Warengruppe::findOrFail($code);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } finally {
            Log::info('Fehlerfrei');
        }
    }

    public function getAll()
    {
        return Warengruppe::all();
    }

    public function create($request)
    {


        Log::info('wgHelper: ' . json_encode($request));
        Log::info(sprintf('wgHelper: parent: %s wg:%s sortiment:%s', $request["parent"], $request["name"], $request["product_range"] )) ;
        // Log::info('wgHelper:', print_r($wgHelper, true));


        $this->wgh = new WgHelper();

        try {
            $this->updateRecFromData($request);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren der Warengruppe: ' . $e->getMessage(), []);
            return false;
        }




        try {

            // wg suchen
            $wgh = WgHelper::where('name', $this->wgh->name)->where('sortiment', $this->wgh->sortiment)->first();

            if (!empty($wgh) && $wgh->id){
                // wg gefunden
                return $wgh->id;
            }
            else {
                // wgh nicht gefunden neu speichern
                $this->wgh->save();
                return $this->wgh->id;
            }

        } catch (\Exception $e) {
            $this->logMessage('error', 'Warengruppe: Fehler beim Speichern der Warengruppe : ' . $e->getMessage(), []);
            return false;
        }
    }

    public function update($id, $wgHelper)
    {
        Log::info(sprintf('WarengruppeRository->update( %d)', $id), [ 'wgHelper' => $wgHelper]);
        // Warengruppe nach ID aus WgHelper suchen
        $wgh = WgHelper::where('id', $id)->first();
        if (empty($wgh)){
            Log::info('wgh ist leer');
            return null;
        }
        else{
            Log::info(sprintf('wgh NICHT leer : %s', $wgh->wgnr ?? 'null'));
        }
        if (!empty($wgh->wgnr)){
            $warengruppe = Warengruppe::where('wgnr', $wgh->wgnr)->first();
            if (empty($warengruppe)){
                Log::info('Warengruppe nicht gefunden ');
                $warengruppe = Warengruppe::Create([
                    'wgnr' => $wgh->wgnr,
                    'bezeichnung' => $wgh->name ]);
            }
            else{
                Log::info('Warengruppe gefunden ');
            }
        }
        return $wgh;
    }

    public function delete($wgnr)
    {
        $warengruppe = Warengruppe::where('wgnr', $wgnr)->first();
        if (!empty($warengruppe)){
            $warengruppe->delete();
        }
    }



    protected function validateRec($rec): bool
    {
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->wgnr) || !is_scalar($rec->wgnr)) {
            $this->logMessage('warning', 'WgNr ist ungültig oder fehlt.', ['wgnr' => $rec->wgnr]);
            return false;
        }

        if (!isset($rec->bezeichnung) || !is_scalar($rec->bezeichnung)) {
            $this->logMessage('warning', 'sortiment ist ungültig oder fehlt.', ['bezeichnung' => $rec->bezeichnung]);
            return false;
        }

        return true;
    }

    function updateRecFromData($request) {

        $this->wgh->id = $request['parent'];
        // $this->wgh->wgnr = $request['wgnr'];
        $this->wgh->name = $request['name'];
        $this->wgh->sortiment = $request['product_range'];






    }
}
