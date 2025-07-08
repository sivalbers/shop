<?php

namespace App\Repositories;

use App\Models\ArtikelSortiment;
use App\Models\Sortiment;
use Illuminate\Support\Facades\Log;

class ArtikelSortimentRepository
{
    private string $logLevel;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = config('logging.artikel_sortiment_repository_log_level', 'error');
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

    public function getAll()
    {
        return ArtikelSortiment::all();
    }

    protected function validateRec($rec): bool
    {
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->artikelnr) || !is_scalar($rec->artikelnr)) {
            $this->logMessage('warning', 'Artikelnummer ist ungültig oder fehlt.', ['artikelnr' => $rec->artikelnr]);
            return false;
        }

        // Prüfen, ob `sortiment` gesetzt und gültig ist
        if (!isset($rec->sortiment) || !is_scalar($rec->sortiment)) {
            $this->logMessage('warning', 'Sortiment ist ungültig oder fehlt.', ['sortiment' => $rec->sortiment]);
            return false;
        }

        return true;
    }

    public function create(array $data)
    {

        // Daten in ein Artikelsortiment übernehmen
        try {
            $rec = $this->updateRecordFromData( $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des ArtikelSortiments: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        $sortiment = ArtikelSortiment::where('artikelnr', $rec->artikelnr)
                                    ->where('sortiment', $rec->sortiment)
                                    ->first();
        try{
            if (empty($sortiment)){
                // Sortiment nicht gefunden
                // Datenvalidieren
                if (!$this->validateRec($rec)) {
                    // Bei Fehler raus.
                    return false;
                }
                // Sonst speichern
                if ($rec->save()) {
                    $this->logMessage('info', 'ArtikelSortiment erfolgreich gespeichert.', ['data' => $data]);
                    return true;
                }
            }
            else{
                // Das Sortiment gibt es schon. Es ist nichts zu tun.
                return true ;
            }
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim Speichern des ArtikelSortiments: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function update($id, array $data)
    {
        try {
            $rec = ArtikelSortiment::findOrFail($id);
            $rec->update($data);
            $this->logMessage('info', "ArtikelSortiment mit ID {$id} erfolgreich aktualisiert.", ['data' => $data]);
            return $rec;
        } catch (\Throwable $e) {
            $this->logMessage('error', "Fehler beim Aktualisieren des ArtikelSortiments mit ID {$id}: " . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function delete($artikelNr, $sortimentId)
    {
        try {
            $sortimentBezeichnung = Sortiment::where('id', $sortimentId)->value('bezeichnung');

            if ($sortimentBezeichnung) {
                ArtikelSortiment::where('artikelnr', $artikelNr)
                    ->where('sortiment', $sortimentBezeichnung)
                    ->delete();
            }
            $this->logMessage('info', "Artikel-Sortiment-Zuordnung wurde gelöscht. {$artikelNr} -> {$sortimentBezeichnung} wurden gelöscht.");
            return true;
        } catch (\Throwable $e) {
            $this->logMessage('error', "Fehler beim Löschen aller ArtikelSortimente mit Artikelnummer {$artikelNr}: " . $e->getMessage());
            return false;
        }
    }

    public function deleteAllbyArtikelnr($artikelNr)
    {
        try {
            ArtikelSortiment::where('artikelnr', $artikelNr)->delete();
            $this->logMessage('info', "Alle ArtikelSortimente mit Artikelnummer {$artikelNr} wurden gelöscht.");
        } catch (\Throwable $e) {
            $this->logMessage('error', "Fehler beim Löschen aller ArtikelSortimente mit Artikelnummer {$artikelNr}: " . $e->getMessage());
        }
    }


    public function updateRecordFromData($data)
    {
        $artikelSortiment = new Artikelsortiment();

        $mapping = [
            'item_number'     => 'artikelnr',
            'product_range'   => 'sortiment',
        ];

        foreach ($mapping as $dataKey => $varKey) {
            if (isset($data[$dataKey])) {
                $artikelSortiment->$varKey = $data[$dataKey];
            } else {
                $this->logMessage('warning', "Datenfeld '{$dataKey}' fehlt. ", ['data' => $data]);
            }
        }

        if (isset($artikelSortiment->sortiment)) {
            $artikelSortiment->sortiment = strtoupper($artikelSortiment->sortiment);
        } else {
            $this->logMessage('error', "Sortiment wurde nicht gesetzt und bleibt NULL!", ['data' => $data]);
        }

        return $artikelSortiment;
    }

}
