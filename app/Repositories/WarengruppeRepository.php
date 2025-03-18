<?php

namespace App\Repositories;

use App\Models\Warengruppe;
use App\Models\wghelper;

use Illuminate\Support\Facades\Log;

class WarengruppeRepository
{

    private string $logLevel;

//#REGION Logging
public function __construct()
{
    // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
    $this->logLevel = config('logging.warengruppe_repository_log_level', 'debug');

    Log::info(['Loglevel' => $this->logLevel]);
}

public function setLogLevel(string $level): void
{
    $this->logLevel = $level;
}

private function shouldLog(string $level): bool
{
    $allowedLogLevels = [
        'debug'   => 0,
        'info'    => 1,
        'warning' => 2,
        'error'   => 3,
    ];

    return $allowedLogLevels[$level] >= $allowedLogLevels[$this->logLevel];
}

private function logMessage(string $level, string $message, array $context = []): void
{
    if ($this->shouldLog($level)) {
        Log::$level($message, $context);
    }
}
//#REGIONEND


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

    public function create($wgHelper)
    {
        $wg = new Warengruppe();

        try {
            $wg = $this->updateRecFromData($wg, $wgHelper);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren der Warengruppe: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            // Validierung des Datensatzes
            if (!$this->validateRec($wg)) {
                return false;
            }

            if ($wg->save()) {
                return true;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            $this->logMessage('warning', 'Warengruppe konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Warengruppe: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function update($id, $wgHelper)
    {
        $warengruppe = Warengruppe::findOrFail($wgHelper->wgnr);
        $warengruppe->bezeichnung = $wgHelper->name;
        $warengruppe->save();
        return $warengruppe;
    }

    public function delete($id)
    {
        $warengruppe = Warengruppe::findOrFail($id);
        $warengruppe->delete();
    }

    function updateRecFromData($warengruppe, $wgHelper) {

        $warengruppe->wgnr = $wgHelper->wgnr;
        $warengruppe->bezeichnung = $wgHelper->name;

        return $warengruppe;

    }
}
