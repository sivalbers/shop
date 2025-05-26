<?php

namespace App\Repositories;

use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\WgHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArtikelRepository
{


    private string $logLevel;
    private string $category_id;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = config('logging.artikel_repository_log_level', 'error');

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

    public function create(array $data) {
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
                return $artikel ;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            $this->logMessage('warning', 'Artikel konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Create: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function update($id, array $data): bool{
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
            if ($this->validateRec($artikel) && $artikel->save()){
                return true;
                //return $artikel;
            }
            else{
                return false;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            Log::warning('Artikel konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            Log::error('Update: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    private function abhaengigeDatenAendern($artikel){
        // $this->category_id wird beim Artikel mitgegeben.
        if (!empty($this->category_id)){
            // Aus der Warengruppen Hilfsdatei die Warengruppen ID die beim Artikel mitgeliefert wurde laden.
            $wgHelper = WgHelper::findOrFail($this->category_id); // Felder: id, wgnr, name, sortiment

            if (!empty($artikel->wgnr)){
                $wg = Warengruppe::where( 'wgnr', $artikel->wgnr)->first();
                if (empty($wg)){
                    $wg = Warengruppe::Create([
                            'wgnr' => $artikel->wgnr,
                            'bezeichnung' => $wgHelper->name
                        ]);
                }
                else {
                    $wg->bezeichnung = $wgHelper->name;
                    $wg->save();
                }
            }

        }
        if (!$artikel->wgnr){
            $artikel->wgnr = 'null';
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
            'item_image'         => 'IMAGE'
        ];
        foreach ($mapping as $dataKey => $artikelKey) {
            if (isset($data[$dataKey])) {
                if ($artikelKey != 'IMAGE'){
                    Log::info(['Artikel->'.$artikelKey => $data[$dataKey]]);
                    $artikel->$artikelKey = $data[$dataKey];
                }
                else {
                    Log::info(['Image' => $this->storeItemImage($data[$dataKey], $artikel->artikelnr)]);
                }
            } else {
                $this->logMessage('warning', "Datenfeld '{$dataKey}' fehlt. ", ['data' => $data]);
            }
        }

        $this->category_id = $data['category_id'];

        return $artikel;

    }


    function storeItemImage($base64Image, $artikelnr)
    {
        // Prüfe, ob ein Bild vorhanden ist
        if (!$base64Image) {
            return null;
        }

        // Extrahiere Dateityp (png oder jpeg) und eigentliche Bilddaten
        list($format, $base64Data) = explode(':', $base64Image);

        // Definiere den MIME-Typ basierend auf dem Format
        $mimeType = ($format == 'png') ? 'image/png' : 'image/jpeg';
        $extension = ($format == 'png') ? 'png' : 'jpg';

        // Base64-Daten dekodieren
        $imageData = base64_decode($base64Data);

        // Generiere einen einzigartigen Dateinamen
        $fileName = $artikelnr . '.' . $extension;

        // Speicherort in Laravel (z.B. storage/app/public/items/)
        $path = "public/products/{$fileName}";

        // Speichere das Bild mit Laravel's Storage-Funktion
        Storage::put($path, $imageData);

        // Rückgabe des Speicherpfads für die Datenbank
        return $path;
    }


}
