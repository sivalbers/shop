<?php

namespace App\Repositories;

use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\WgHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Config;
use App\Repositories\ImageRepository;

class ArtikelRepository
{


    private string $logLevel;
    private string $category_id;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = Config::globalString('logging.artikel_repository_log_level', 'info');

        Log::info(['logging.artikel_repository_log_level' => $this->logLevel]);


            $this->logMessage('debug', 'Test debug');
            $this->logMessage('info', 'Test info');
            $this->logMessage('warning', 'Test warning');
            $this->logMessage('error', 'Test error');

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

    private function logFailure(string $context, \Throwable $e, array $data = []): void
    {
        Log::error("[$context] Exception: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'data' => $data
        ]);
    }

    private function getArtikelOrNew($artikelnr){
        $artikel = Artikel::where('artikelnr', $artikelnr )->first();
        if (!$artikel){
            $this->logMessage('debug', 'Artikel wurde nicht gefunden!');
            $artikel = new Artikel();
        }
        else {
            $this->logMessage('debug', 'Artikel wurde gefunden!');
        }
        return $artikel;
    }

    public function create(array $data) {

        $artikel = $this->getArtikelOrNew($data['item_number']);

        try {
            $artikel = $this->updateArtikelFromData($artikel, $data);
            $this->logMessage('debug', 'Data wurde zu Artikel übernommen!');
        } catch (\Throwable $e) {

            $this->logMessage('error', 'ArtikelRepository->Create:: Fehler beim konvertieren des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return null;
        }

        try {

            if (!$this->validateRec($artikel)) {
                return null;
            }

            $this->logMessage('debug', 'validateRec bestanden.');

            if ($artikel->save()) {
                $this->logMessage('debug', 'ArtikelRepository->Artikel wurde gespeichert.');
                return $artikel ;
            }


            $this->logMessage('warning', 'ArtikelRepository->Artikel konnte nicht gespeichert werden.', ['data' => $data]);
            return null;

        } catch (\Exception $e) {
            $this->logMessage('error', 'ArtikelRepository->Create: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return null;
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

                if (!empty($data['category_id'])){
                    $wgHelper = WgHelper::where('id', $data['category_id'])->first();
                    if (!$wgHelper){
                        $wgHelper = WgHelper::create();
                        $wgHelper->wgnr = $artikel->wgnr;
                        $wgHelper->save();
                    }
                }
                return true;

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
        $this->logMessage('debug', 'Anfang updateArtikelFromData');
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
                    // Log::info(['Artikel->'.$artikelKey => $data[$dataKey]]);

                    //$this->logMessage('debug', 'Feld: '.$artikelKey, [ $data[$dataKey] ]);
                    $artikel->$artikelKey = $data[$dataKey];
                }
                else {
                    $imageRepository = new ImageRepository();
                    Log::info(['Image' => $imageRepository->storeItemImage($data[$dataKey], $artikel->artikelnr)]);
                }
            } else {
                $this->logMessage('warning', "Datenfeld '{$dataKey}' fehlt. ", ['data' => $data]);
            }
        }
        $blocked = (isset($data['blocked'])) ? $data['blocked'] === 'Ja' : false ;
        $blockedVk = (isset($data['blockedvk'])) ? $data['blockedvk'] === 'Ja' : false ;

        $artikel->gesperrt = $blocked or $blockedVk;
        $this->category_id = $data['category_id'];

        return $artikel;

    }

/*
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
*/

}
