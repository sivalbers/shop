<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ImportController;

use App\Livewire\ArtikelComponent;
use App\Livewire\SortimentComponent;
use App\Livewire\ArtikelSortimentComponent;
use App\Livewire\WarengruppeComponent;
use App\Livewire\ShopComponent;
use App\Livewire\AnschriftComponent;
use App\Livewire\BestellungComponent;
use App\Livewire\TestMainComponent;
use App\Livewire\NachrichtComponent;
use App\Livewire\NachrichtenListComponent;
use App\Livewire\ApiLogComponent;
use App\Livewire\ImportComponent;
use App\Livewire\BelegarchivComponent;

use App\Models\Config;

use App\Http\Controllers\ODataController;
use App\Http\Controllers\PunchOut;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiLogMiddleware;

use App\Livewire\ArtikelSuche;

use App\Mail\ExampleMail;

// use Dotenv\Dotenv;


Route::get('/', function () {
    return redirect('/startseite');
});


Route::view('startseite', 'startseite')
//    ->middleware(['auth', 'verified'])
    ->middleware(['auth' ])
    ->name('startseite');


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/shop', ShopComponent::class)
    ->middleware(['auth'])
    ->name('shop');

Route::get('/bestellungen', BestellungComponent::class)
    ->middleware(['auth'])
    ->name('bestellungen');

Route::get('/testmain', TestMainComponent::class)
    ->middleware(['auth'])
    ->name('testmain');

Route::get('/nachrichten', NachrichtComponent::class)
    ->middleware(['auth'])
    ->name('nachrichten');

Route::get('/news', NachrichtenListComponent::class)
    ->middleware(['auth'])
    ->name('news');


Route::middleware([AdminMiddleware::class])->group(function(){
    Route::get('/artikels', ArtikelComponent::class)->middleware('auth')->name('artikel');
    Route::get('/sortimente', SortimentComponent::class)->middleware('auth')->name('sortimente');
    Route::get('/artikel-sortimente', ArtikelSortimentComponent::class)->middleware('auth'); // Fehler
    Route::get('/warengruppen', WarengruppeComponent::class)->middleware('auth')->name('warengruppen');



    Route::get('/import-odata/artikel', [ODataController::class, 'importArtikel'])->name('importArtikel');
    Route::get('/import-odata/artikelbestand', [ODataController::class, 'importArtikelBestand'])->name('importArtikelBestand');
    Route::get('/import-odata/wg', [ODataController::class, 'importWarengruppe'])->name('importWG');
    Route::get('/import-odata/sortiment', [ODataController::class, 'importSortiment'])->name('importSortiment');

    Route::get('/import/bestellung', [ImportController::class, 'importBestellungenInBestellhistorie'])->name('importBestellung');

    Route::get('/anschriften', AnschriftComponent::class)->name('anschriften');

    Route::get('/import', ImportComponent::class)->name('import');

    Route::get('/apilog', ApiLogComponent::class)->middleware('auth')->name('apilog');
    Route::view('/apitest', 'apitest')->name('apitest');
    Route::get('/logs', function() {
        return view('logs');
    })->name('logs');

    Route::get('/belegarchiv', BelegarchivComponent::class)->name('belegarchiv'); // Fehler beim holen der Belege ...

});

// Route::get('/suchtest', ArtikelSuche::class); // Funktioniert, aber ausgeklammert. Bildet die Artikelsuche ab.
Route::post('/punchout', [Punchout::class, 'handlePunchOutPost']);
Route::get('/punchout', [PunchOut::class, 'handlePunchOutGet']);

Route::view('/datenschutz', 'datenschutz')->name('datenschutz');
Route::view('/impressum', 'impressum')->name('impressum');

/*
Route::get('/send-email', function () {

    // Teste, ob die Datei geladen wird
    //$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    //$dotenv->load();

    //dd($_ENV['MAIL_MAILER'], getenv('MAIL_MAILER'), env('MAIL_MAILER'));

    $details = [
        'title' => 'Test-E-Mail von Laravel',
        'body' => 'Dies ist ein Test-E-Mail-Versand.'
    ];

    Mail::to('mail@andreasalbers.de')->send(new ExampleMail($details));

    return 'E-Mail wurde gesendet!';
});
*/

// fragt beim Live-Shop an, wegen einer Session-ID

Route::middleware([ApiLogMiddleware::class])->group(function () {
    Route::get('/api/{url}', [ApiController::class, 'verarbeiteApiUrlGet']);
    Route::post('/api/{url}', [ApiController::class, 'verarbeiteApiUrlPost']);
    Route::patch('/api/{url}', [ApiController::class, 'verarbeiteApiUrlPatch']);
    Route::delete('/api/{url}', [ApiController::class, 'verarbeiteApiUrlDelete']);

    Route::get('/api/{url}/{id}', [ApiController::class, 'verarbeiteApiUrlGet']);
    Route::post('/api/{url}/{id}', [ApiController::class, 'verarbeiteApiUrlPost']);
    Route::patch('/api/{url}/{id}', [ApiController::class, 'verarbeiteApiUrlPatch']);
    Route::delete('/api/{url}/{id}', [ApiController::class, 'verarbeiteApiUrlDelete']);

    Route::delete('/api/{url}/{artikel}/{id}', [ApiController::class, 'verarbeiteApiUrlProductDelete']);
});





// Route ohne Parameter
Route::get('/test', function () {
    return "http://shop.local/test";
    //return " >>>". Config::globalString($var);
});





Route::middleware([AdminMiddleware::class])->group(function(){
    // Route mit Parameter (z.B. $id)
    Route::get('/config/{wert}', function ($wert) {
        $globalString = Config::globalString($wert);
        $globalJson = Config::globalJson($wert);

        $result = "<div class='font-bold'>Global</div><br>";
        $result = $result . sprintf("Werte: >%s< <br>", $wert );
        $result = $result . sprintf ("  globalString('%s') <br>", $globalString);
        $result = $result . sprintf ("  JSON: %s<br>", $globalJson);
        $array = json_decode($globalJson);

        $result = $result . sprintf("  Json-array: %s<br>", print_r($array, true));

        try {
            $result = $result . sprintf ("<a href='%s'>%s</a><br>", route($globalString), route($globalString));
        } catch (Exception $e) { // Fange die Exception ab

            $result = $result . "Fehler: " . $e->getMessage() . "<br>";
        }

        $jsonString = json_encode($array);

        return $result ;

    });
    Route::get('/config/{wert}/{kundennr}', function ($wert, $kundennr) {
        $kundennrString = Config::kundennrString($wert, $kundennr);
        $kundennrJson = Config::kundennrJson($wert, $kundennr);

        $result = "<div class='font-bold'>Kundennr</div><br>";
        $result = sprintf("Kundennr Werte: >%s< kundennr: >%s<<br>", $wert, $kundennr );
        $result = $result . sprintf ("  globalString('%s') <br>", $kundennrString);
        $result = $result . sprintf ("  JSON: %s<br>", $kundennrJson);
        $array = json_decode($kundennrJson);

        $result = $result . sprintf("  Json-array: %s<br>", print_r($array, true));

        try {
            $result = $result . sprintf ("<a href='%s'>%s</a><br>", route($kundennrString), route($kundennrString));
        } catch (Exception $e) { // Fange die Exception ab

            $result = $result . "Fehler: " . $e->getMessage() . "<br>";
        }

        $jsonString = json_encode($array);

        return $result ;

    });

    Route::get('/config/{wert}/{kundennr}/{userid}', function ($wert, $kundennr, $userid) {
        $startTime = microtime(true);
        $kundennrString = Config::kundennrString($wert, $kundennr);
        $kundennrJson = Config::kundennrJson($wert, $kundennr);

        $result = "<div class='font-bold'>Kundennr</div><br>";
        $result = sprintf("Kundennr Werte: >%s< KundenNr: >%s< User-ID: >%s<<br>", $wert, $kundennr, $userid );
        $result = $result . sprintf ("  globalString('%s') <br>", $kundennrString);
        $result = $result . sprintf ("  JSON: %s<br>", $kundennrJson);
        $array = json_decode($kundennrJson);

        $result = $result . sprintf("  Json-array: %s<br>", print_r($array, true));

        try {
            $result = $result . sprintf ("<a href='%s'>%s</a><br>", route($kundennrString), route($kundennrString));
        } catch (Exception $e) { // Fange die Exception ab

            $result = $result . "Fehler: " . $e->getMessage() . "<br>";
        }

        $jsonString = json_encode($array);


        $endTime = microtime(true);

        // Berechne die Differenz (in Sekunden)
        $executionTime = $endTime - $startTime;

        $result = $result . "<br><br>Die Funktion hat $executionTime Sekunden benötigt.";

        $startTime = microtime(true);
        $result = $result . "<br>".env('test');
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedTime = number_format($executionTime, 8);

        $result = $result . "<br><br>Die Funktion hat $formattedTime Sekunden benötigt.";



        return $result ;
    });
});


require __DIR__.'/auth.php';

