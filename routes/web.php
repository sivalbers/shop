<?php


use Illuminate\Support\Facades\Route;
use App\Livewire\ArtikelComponent;
use App\Livewire\SortimentComponent;
use App\Livewire\ArtikelSortimentComponent;
use App\Livewire\WarengruppeComponent;
use App\Livewire\ShopComponent;
use App\Livewire\AnschriftComponent;
use App\Livewire\BestellungComponent;
use App\Livewire\TestMainComponent;
use App\Livewire\WarenkorbComponent;
use App\Livewire\TestMainUnterComponent;
use App\Livewire\NachrichtComponent;
use App\Livewire\BestellungListComponent;
use App\Models\Config;

use App\Http\Controllers\ODataController;
use App\Http\Controllers\Punchout;

use App\Http\Middleware\AdminMiddleware;

use App\Mail\ExampleMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ApiController;

use Dotenv\Dotenv;

// Route::view('/', 'startseite');
Route::get('/', function () {
    return redirect('/startseite');
});

Route::view('startseite', 'startseite')
    ->middleware(['auth', 'verified'])
    ->name('startseite');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/shop', ShopComponent::class)
    ->middleware('auth')
    ->name('shop');

Route::get('/bestellungen', BestellungComponent::class)
    ->middleware('auth')
    ->name('bestellungen');
/*
Route::get('/shop', BestellungListComponent::class)
    ->middleware('auth')
    ->name('bestellungen');
*/


    /*
    Route::get('/warenkorb', WarenkorbComponent::class)
        ->middleware('auth')
        ->name('warenkorb');
    */

Route::get('/testmain', TestMainComponent::class)
    ->name('testmain');


Route::middleware([AdminMiddleware::class])->group(function(){
    Route::get('/artikels', ArtikelComponent::class)->middleware('auth')->name('artikel');
    Route::get('/sortimente', SortimentComponent::class)->middleware('auth')->name('sortimente');
    Route::get('/artikel-sortimente', ArtikelSortimentComponent::class)->middleware('auth');
    Route::get('/warengruppen', WarengruppeComponent::class)->middleware('auth')->name('warengruppen');

    Route::get('/import-odata/artikel', [ODataController::class, 'importArtikel'])->name('importArtikel');
    Route::get('/import-odata/wg', [ODataController::class, 'importWarengruppe'])->name('importWG');
    Route::get('/import-odata/sortiment', [ODataController::class, 'importSortiment'])->name('importSortiment');

    Route::get('/anschriften', AnschriftComponent::class)->name('anschriften');

    Route::get('/nachrichten', NachrichtComponent::class)->name('nachrichten');

    Route::view('apitest', 'apitest')->name('apitest');

});


Route::post('/punchout', [Punchout::class, 'handlePunchOut']);


Route::get('/send-email', function () {

    // Teste, ob die Datei geladen wird
    //$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    //$dotenv->load();

    //dd($_ENV['MAIL_MAILER'], getenv('MAIL_MAILER'), env('MAIL_MAILER'));
    dd(env('MAIL_MAILER'));
    $details = [
        'title' => 'Test-E-Mail von Laravel',
        'body' => 'Dies ist ein Test-E-Mail-Versand.'
    ];

    Mail::to('mail@andreasalbers.de')->send(new ExampleMail($details));

    return 'E-Mail wurde gesendet!';
});


// fragt beim Live-Shop an, wegen einer Session-ID
Route::get('/api/session', [ApiController::class, 'buildSessionId']);
Route::get('/api/getsessionid', [ApiController::class, 'getSessionId']);




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

