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

class ODataController extends Controller
{
    public function importArtikel()
    {




        $json = json_decode(Config::kundennrJson('URL_IMPORT', Auth::user()->kundenr), true);

        if (!$json) {
            return response()->json(['error' => 'Fehler beim Import: >URL_IMPORT< nicht gefunden' ], 500);

        }

        $url = $json['URL_IMPORT_ARTIKEL'];

        $username = env('NAV_USER', "testuser");
        $password = env('NAV_PASSWORD', "Sieverding22!");

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

            // Hole den Body der Antwort
            $data = json_decode($response->getBody()->getContents(), true);

            // Verarbeite die Daten (z.B. speichere sie in der Datenbank)
            // Beispiel: Durchlaufe die Daten und speichere sie

            foreach ($data['value'] as $item) {
                $artikel = Artikel::find($item["artikelnr"]);
                if (is_null($artikel)){
                    $artikel = new Artikel();
                }

                $artikel->artikelnr = $item["artikelnr"];
                $artikel->bezeichnung = $item["bezeichnung"];
                $artikel->langtext = $item["langtext"];
                $artikel->gesperrt = $item["gesperrt"] ? true : false ;
                $artikel->verpackungsmenge = 1;
                $artikel->einheit = $item["einheit"];
                $artikel->vkpreis = $item["preis"];
                $artikel->wgnr = $item["wgnr"];

                $artikel->save();
            }

            return response()->json(['message' => 'Daten erfolgreich importiert']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fehler beim Import: ' . $e->getMessage()], 500);
        }
    }

    public function importWarengruppe()
    {

        $json = json_decode(Config::kundennrJson('URL_IMPORT', Auth::user()->kundenr), true);

        if (!$json) {
            return response()->json(['error' => 'Fehler beim Import: >URL_IMPORT< nicht gefunden' ], 500);
        }

        $url = $json['URL_IMPORT_WG'];

        $username = env('NAV_USER', "testuser");
        $password = env('NAV_PASSWORD', "Sieverding22!");


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

            // Hole den Body der Antwort
            $data = json_decode($response->getBody()->getContents(), true);

            // Verarbeite die Daten (z.B. speichere sie in der Datenbank)
            // Beispiel: Durchlaufe die Daten und speichere sie


            foreach ($data['value'] as $item) {
                $wg = Warengruppe::find($item["wgnr"]);
                if (is_null($wg)){
                    $wg = new Warengruppe();
                }
                $wg->wgnr = $item["wgnr"] ;
                $wg->bezeichnung = $item["bezeichnung"] ;

                $wg->save();

                // Hier kannst du die Daten in deinem Modell speichern
                // z.B.: YourModel::create($item);
            }

            return response()->json(['message' => 'Daten erfolgreich importiert']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fehler beim Import: ' . $e->getMessage()], 500);
        }
    }

    public function importSortiment()
    {

        $json = json_decode(Config::kundennrJson('URL_IMPORT', Auth::user()->kundenr), true);

        if (!$json) {
            return response()->json(['error' => 'Fehler beim Import: >URL_IMPORT< nicht gefunden' ], 500);

        }

        $url = $json['URL_IMPORT_SORTIMENT'];

        $username = env('NAV_USER', "testuser");
        $password = env('NAV_PASSWORD', "Sieverding22!");

        // Initialisiere den Guzzle-Client
        $client = new Client();
        $countTotal = 0 ;
        $countImport = 0 ;

        try {
            // Sende die Anfrage an den OData-Dienst mit Basic-Authentifizierung
            $response = $client->request('GET', $url, [
                'auth' => [$username, $password],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            // Hole den Body der Antwort
            $data = json_decode($response->getBody()->getContents(), true);

            // Verarbeite die Daten (z.B. speichere sie in der Datenbank)
            // Beispiel: Durchlaufe die Daten und speichere sie


            foreach ($data['value'] as $item) {
                $countTotal++;

                $ok = true ;
                if ($item["artikelnr"] != '' && $item["sortiment"] != ''){
                    try {
                        $artikel = Artikel::where('artikelnr', $item["artikelnr"])->firstOrFail();

                    } catch (ModelNotFoundException $e) {
                        $ok = false ;
                        Log::error('Sortiment Import Artikel nicht gefunden: ',[$item['artikelnr']]);

                    }

                    try {
                        $sortiment = Sortiment::where('bezeichnung', $item["sortiment"])->firstOrFail();
                    } catch (ModelNotFoundException $e) {
                        $ok = false ;
                        Log::error('Sortiment Import Sortiment nicht gefunden: ',[$item['sortiment']]);
                    }
                    if ($ok){
                       // Log::info(' OK Artikel, Sortiment: ',[$item['artikelnr'], $item['sortiment']]);
                    }
                    else{
                       // Log::info('NOK Artikel, Sortiment: ',[$item['artikelnr'], $item['sortiment']]);
                    }
                    try{
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
                        Log::error("Import Fehler: ",[ $e->getMessage()]);
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
    }


}
