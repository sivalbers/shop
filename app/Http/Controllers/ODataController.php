<?php



namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Artikel;
use App\Models\Warengruppe;
use App\Models\ArtikelSortiment;
use App\Models\Sortiment;
use App\Models\Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ODataController extends Controller
{

    private $error;
    private $errorMessage;
    private $storagePath;


    public function __construct()
    {
        $this->error = false;
        $this->errorMessage = '';
        $this->storagePath = storage_path('app/'); // Korrektes Dateitrennzeichen
    }

    public function importArtikel()
    {

        $data = $this->getJsonData('URL_IMPORT_ARTIKEL');

        //return $this->importArtikel_zeilenweise($data);

        $this->save_json_csv($data, 'artikel.csv');
        $result = $this->importArtikelDirect('artikel.csv');
        //dd($result);
        if ($result) {
            return response()->json(['ok' => 'Import Erfolgreich'], 200);
        } else {
            return response()->json(['error' => $this->errorMessage], 500);
        }
    }

    public function save_json_csv($data, $name)
    {
        $file = $this->storagePath . $name;
        $csvFile = fopen($file, 'w');

        // Prüfen, ob "value" existiert und nicht leer ist
        if (!isset($data['value']) || empty($data['value'])) {
            throw new \Exception('Kein gültiges JSON-Format oder leeres Array.');
        }

        // Erster Eintrag aus "value" für die Header-Zeile verwenden
        fputcsv($csvFile, array_keys($data['value'][0]), ';', '"');

        // Alle Datensätze in CSV schreiben
        foreach ($data['value'] as $row) {
            // Dezimaltrennzeichen von . auf , umstellen
            array_walk($row, function (&$value) {
                if (is_numeric($value) && strpos($value, '.') !== false) {
                    $value = str_replace('.', ',', $value);
                }
            });

            fputcsv($csvFile, $row, ';', '"');
        }

        fclose($csvFile);
    }

    public function importArtikelDirect($name)
    {
        try {
            $escapedPath = DB::getPdo()->quote($this->storagePath . $name);
            Log::info('Vor Update Artikel');
            DB::statement("
                Update artikel set update_status = 3;
            ");
            /*
                LOAD DATA LOCAL INFILE {$escapedPath}
                INTO TABLE artikel_import
                FIELDS TERMINATED BY ';'
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\\n'
                IGNORE 1 LINES
                (artikelnr, einheit, bezeichnung, langtext, @gesperrt, wgnr, @geaendertam, @vkpreis, @AuxiliaryIndex1, @AuxiliaryIndex2)
                SET
                    gesperrt = IF(@gesperrt != '', 1, 0),
                    vkpreis = CAST(REPLACE(@vkpreis, ',', '.') AS DECIMAL(10,2)),
                    updated = 10;
        ");
*/
            // 1. Neue Einträge hinzufügen, vorhandene ignorieren


            Log::info('Vor create Temporary Artikel_import');
            DB::statement("CREATE TEMPORARY TABLE artikel_import LIKE artikel;");
            Log::info('Import: ' . $escapedPath);
            DB::statement("
                LOAD DATA LOCAL INFILE {$escapedPath}
                INTO TABLE artikel_import
                FIELDS TERMINATED BY ';'
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\\n'
                IGNORE 1 LINES
                (artikelnr, einheit, bezeichnung, langtext, @gesperrt, @wgnr, @geaendertam)
                SET
                    gesperrt = IF(@gesperrt != '', 1, 0),
                    wgnr = NULLIF(@wgnr, ''), -- Falls leer, wird NULL gespeichert
                    update_status = 10;
            ");

            Log::info('Import Artikel');
            DB::statement("
                INSERT INTO artikel (artikelnr, einheit, bezeichnung, langtext, gesperrt, wgnr, update_status)
                SELECT artikelnr, einheit, bezeichnung, langtext, gesperrt, wgnr, update_status
                FROM artikel_import
                WHERE wgnr IS NOT NULL AND wgnr != ''
                ON DUPLICATE KEY UPDATE
                    einheit = VALUES(einheit),
                    bezeichnung = VALUES(bezeichnung),
                    langtext = VALUES(langtext),
                    gesperrt = VALUES(gesperrt),
                    wgnr = VALUES(wgnr),
                    update_status = 1;
            ");
            Log::info('Vor Drop temp table');
            DB::statement("DROP TEMPORARY TABLE artikel_import;");





            return true;
        } catch (\Exception $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
            Log::error($this->errorMessage);
            return false;
        }
    }


    private function get($item, $fld)
    {
        try {
            //log::info(['get' => $fld]);

            if (!array_key_exists($fld, $item)) {
                Log::error(['Fehler' => "Schlüssel '{$fld}' existiert nicht im Array"]);
                return null; // oder Standardwert setzen
            }

            return $item[$fld];
        } catch (\Exception $e) {
            Log::error(['Fehler' => $e->getMessage()]);
            return null;
        }
    }


    public function importArtikel_zeilenweise($data)
    {
        if (!empty($data)) {

            Log::info('Beginne mit dem Import der Daten');

            try {

                if (!isset($data['value']) || !is_array($data['value'])) {
                    Log::error(['Fehler' => 'Datenstruktur ungültig', 'Daten' => $data]);
                    return false;
                }
                foreach ($data['value'] as $i => $item) {

              //      Log::info(['Zeile: ' => $i+1, 'Item' => $item]);

                    if (!is_array($item)) {
                        Log::error([
                            'Fehler' => "Erwartetes Array nicht gefunden",
                            'Key' => $i,
                            'Gefunden' => $item
                        ]);
                        continue;
                    }

                    $artikelnr = $this->get($item, 'artikelnr');

                    if (!empty($artikelnr)) {
                        $artikel = Artikel::find($artikelnr);
                        if (is_null($artikel)) {
                            $artikel = new Artikel();
                        }

                        $artikel->artikelnr = $artikelnr;
                        $artikel->bezeichnung = $this->get($item, "bezeichnung");
                        $artikel->langtext = $this->get($item, "langtext");
                        $artikel->gesperrt = $this->get($item, "gesperrt") ? true : false;
                        $artikel->verpackungsmenge = 1;
                        $artikel->einheit = $this->get($item, "einheit");
                        $artikel->wgnr = $this->get($item, "wgnr");
                        $artikel->update_status = 1;
                        $artikel->save();
                    } // ende If
                    else {
                        Log::info([ 'Fehler' => 'Artikelnummer ist leer']);
                    }
                }
                Log::info (['Fertig Zeilen Importiert: ' => $i]);
                return true;
            } catch (\Exception $e) {
                Log::error(['Fehler' => $e->getMessage(), 'Zeile: ' => $item, 'Artikel' => $artikelnr ]);
                $this->error = true;
                $this->errorMessage = 'Fehler beim Artikelimport: ' . $e->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }

    public function importWarengruppe()
    {

        $data = $this->getJsonData('URL_IMPORT_WG');

        if (!empty($data)) {

            Log::info('beginne mit dem Import der Daten');

            try {

                foreach ($data['value'] as $item) {
                    $wg = Warengruppe::find($item["wgnr"]);
                    if (is_null($wg)) {
                        $wg = new Warengruppe();
                    }
                    $wg->wgnr = $item["wgnr"];
                    $wg->bezeichnung = $item["bezeichnung"];

                    $wg->save();

                    // Hier kannst du die Daten in deinem Modell speichern
                    // z.B.: YourModel::create($item);
                }

                return response()->json(['message' => 'Daten erfolgreich importiert']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Fehler beim Import: ' . $e->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => $this->errorMessage], 500);
        }
    }

    public function importSortiment()
    {
        Log::info('ImportSortiment');

        $data = $this->getJsonData('URL_IMPORT_SORTIMENT');

        if (!empty($data)) {

            Log::info('beginne mit dem Import der Daten');

            try {
                $countTotal = 0;
                $countImport = 0;
                foreach ($data['value'] as $item) {
                    $countTotal++;

                    $ok = true;
                    if ($item["artikelnr"] != '' && $item["sortiment"] != '') {
                        try {
                            $artikel = Artikel::where('artikelnr', $item["artikelnr"])->firstOrFail();
                        } catch (ModelNotFoundException $e) {
                            $ok = false;
                            Log::error('Sortiment Import Artikel nicht gefunden: ', [$item['artikelnr']]);
                        }

                        try {
                            $sortiment = Sortiment::where('bezeichnung', $item["sortiment"])->firstOrFail();
                        } catch (ModelNotFoundException $e) {
                            $ok = false;
                            Log::error('Sortiment Import Sortiment nicht gefunden: ', [$item['sortiment']]);
                        }
                        if ($ok) {
                            // Log::info(' OK Artikel, Sortiment: ',[$item['artikelnr'], $item['sortiment']]);
                        } else {
                            // Log::info('NOK Artikel, Sortiment: ',[$item['artikelnr'], $item['sortiment']]);
                        }
                        try {
                            if ($ok) {
                                // Wenn die Artikelnummer existiert, wird das Artikelobjekt zurückgegeben
                                $countImport++;

                                $artikelsortiment = ArtikelSortiment::firstOrCreate(
                                    [
                                        'artikelnr' => $item["artikelnr"],
                                        'sortiment' => $item["sortiment"]
                                    ],
                                    [
                                        // Hier kannst du zusätzliche Standardwerte setzen, wenn der Datensatz erstellt wird.
                                        // 'spalte1' => 'Standardwert1',
                                        // 'spalte2' => 'Standardwert2',
                                    ]
                                );
                            }
                        } catch (ModelNotFoundException $e) {

                            // Hier wird die Ausnahme abgefangen, wenn die Artikelnummer nicht existiert
                            // Du kannst hier eine Nachricht zurückgeben, eine Weiterleitung durchführen, etc.
                            // return response()->json(['error' => 'Artikelnummer existiert nicht.'], 404);
                            Log::error("Import Fehler: ", [$e->getMessage()]);
                        }
                    }


                    // Hier kannst du die Daten in deinem Modell speichern
                    // z.B.: YourModel::create($item);
                }
                $message = sprintf("%d Datens&auml;tze gelesen, davon %d Datens&auml;tze importiert", $countTotal, $countImport);
                return response()->json(['message' => $message]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Fehler beim Import: ' . $e->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => $this->errorMessage], 500);
        }
    }

    public function getJsonData($url)
    {
        $json = json_decode(Config::kundennrJson('URL_IMPORT', Auth::user()->kundenr), true);

        if (!$json) {
            return response()->json(['error' => 'Fehler beim Import: >URL_IMPORT< nicht gefunden'], 500);
        }

        $url = $json[$url];

        $username = env('NAV_USER', "testuser");
        $password = env('NAV_PASSWORD', "Sieverding22!!");

        // Initialisiere den Guzzle-Client
        $client = new Client();

        try {
            // Sende die Anfrage an den OData-Dienst mit Basic-Authentifizierung
            $response = $client->request('GET', $url, [
                'auth' => [$username, $password],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            Log::info('fehlerfrei');

            // Hole den Body der Antwort
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::info('Fehler' . $e->getMessage());
            $this->error = true;
            $this->errorMessage = 'Fehler beim Import: ' . $e->getMessage();
            return null;
        }
    }
}
