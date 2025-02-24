<?php

namespace App\Repositories;

use App\Models\Artikel;
use Illuminate\Support\Facades\Log;

class ArtikelRepository
{


    private string $logLevel;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = config('logging.artikel_repository_log_level', 'error');
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
        if (!isset($rec->artikelnr) || !is_scalar($rec->artikelnr)) {
            $this->logMessage('warning', 'Artikelnummer ist ungültig oder fehlt.', ['artikelnr' => $rec->artikelnr]);
            return false;
        }

        return true;
    }

    public function getAll()
    {
        return Artikel::all();
    }

    public function create(array $data)
    {
        $artikel = new Artikel();

        try {
            $artikel = $this->updateArtikelFromData($artikel, $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            // Validierung des Datensatzes
            if (!$this->validateRec($artikel)) {
                return false;
            }

            if ($artikel->save()) {
                return true;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            $this->logMessage('warning', 'Artikel konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Create: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function update($id, array $data)
    {
        Log::info([ 'data' => $data]);
        try {
            $artikel = Artikel::findOrFail($id);

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Update:: Artikelnr nicht gefunden: ' . $e->getMessage(), ['artikelnr' => $id]);
            return false;
        }

        try {
            $artikel = $this->updateArtikelFromData($artikel, $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            return ($this->validateRec($artikel) && $artikel->save());

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            Log::warning('Artikel konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            Log::error('Update: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }


    public function delete($id)
    {
        Log::info(['delete'=> $id]);

        try {
            $artikel = Artikel::findOrFail($id);


        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'delete:: Artikel wurde nicht gefunden: ' . $e->getMessage(), ['artikelnr' => $id]);
            return false;
        }
        try {

            $artikel->delete();
            return true;

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'delete:: Artikel konnte nicht gelöscht werden: ' . $e->getMessage(), ['artikelnr' => $id]);
            return false;
        }

    }


    function updateArtikelFromData($artikel, $data) {
        // Mapping der Spalten von `data` zu `Artikel`

        $mapping = [
            'item_number'        => 'artikelnr',
            'item_name'          => 'bezeichnung',
            'tax_rate'           => 'steuer',
            'price'              => 'vkpreis',
            'quantity'           => 'bestand',
            'item_description'   => 'langtext',
            'packing_quantity'   => 'verpackungsmenge',
            'sales_unit'         => 'einheit',
            'unspsc'             => 'wgnr',
        ];
        foreach ($mapping as $dataKey => $artikelKey) {
            if (isset($data[$dataKey])) {
                $artikel->$artikelKey = $data[$dataKey];
            } else {
                $this->logMessage('warning', "Datenfeld '{$dataKey}' fehlt. ", ['data' => $data]);
            }
        }
        if (!$artikel->wgnr){
            $artikel->wgnr = 'null';
        }

        return $artikel;

    }


}
