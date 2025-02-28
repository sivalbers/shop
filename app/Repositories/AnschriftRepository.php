<?php

namespace App\Repositories;

use App\Models\Anschrift;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnschriftRepository
{


    private string $logLevel;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = config('logging.anschrift_repository_log_level', 'error');

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
        if (!isset($rec->kundennr) || !is_scalar($rec->kundennr)) {
            $this->logMessage('warning', 'Kundennr ist ungültig oder fehlt.', ['kundennr' => $rec->kundennr]);
            return false;
        }

        if (!isset($rec->strasse) || !is_scalar($rec->strasse)) {
            $this->logMessage('warning', 'strasse ist ungültig oder fehlt.', ['strasse' => $rec->strasse]);
            return false;
        }


        if (!isset($rec->plz) || !is_scalar($rec->plz)) {
            $this->logMessage('warning', 'plz ist ungültig oder fehlt.', ['plz' => $rec->plz]);
            return false;
        }

        if (!isset($rec->stadt) || !is_scalar($rec->stadt)) {
            $this->logMessage('warning', 'stadt ist ungültig oder fehlt.', ['stadt' => $rec->stadt]);
            return false;
        }

        if (!isset($rec->land) || !is_scalar($rec->land)) {
            $this->logMessage('warning', 'land ist ungültig oder fehlt.', ['land' => $rec->land]);
            return false;
        }

        return true;
    }

    public function getAll()
    {
        return Anschrift::all();
    }

    public function create(array $data)
    {
        Log::info('Create-Anschrift');
        $anschrift = new Anschrift();

        try {
            $anschrift = $this->updateAnschriftFromData($anschrift, $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren der Anschrift: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            // Validierung des Datensatzes
            if (!$this->validateRec($anschrift)) {
                Log::info('Fehler in Validate');
                return false;
            }

            $id = $anschrift->save();
            if ($id) {
                return $id;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            $this->logMessage('warning', 'anschrift konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Create: Fehler beim Speichern des Anschrift: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function update($kundennr, array $data)
    {
        Log::info([ 'data' => $data]);
        try {
            $anschrift = Anschrift::findOrFail($kundennr);

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Update:: Anschrift nicht gefunden: ' . $e->getMessage(), ['kundennr' => $kundennr]);
            return false;
        }

        try {
            $artikel = $this->updateArtikelFromData($anschrift, $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren der anschrift: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            return ($this->validateRec($anschrift) && $anschrift->save());

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            Log::warning('anschrift konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            Log::error('Update: Fehler beim Speichern des anschrift: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }


    public function delete($kundennr)
    {
        Log::info(['delete'=> $kundennr]);

        try {
            $anschrift = Anschrift::findOrFail($kundennr);


        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'delete:: anschrift wurde nicht gefunden: ' . $e->getMessage(), ['Kundennr' => $kundennr]);
            return false;
        }
        try {

            $anschrift->delete();
            return true;

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'delete:: anschrift konnte nicht gelöscht werden: ' . $e->getMessage(), ['Kundennr' => $kundennr]);
            return false;
        }

    }

    function updateAnschriftFromData($rec, $data) {

        $rec->kundenr           = $data['user_id'];
        $rec->kurzbeschreibung  = $data['company'];
        $rec->firma1            = $rec->kurzbeschreibung;
        $rec->strasse           = $data['street'];
        $rec->plz               = $data['zipcode'];
        $rec->stadt             = $data['city'];
        if ($data['billing'] && $data['delivery']){
            $rec->art = '';
        }
        else
            if ($data['billing']){
                $rec->art = 'Rechnungsadresse';
            }
            else
                if ($data['billing']){
                    $rec->art = 'Lieferadresse';
                }
        Log::info($rec);
        return $rec;
    }


}
