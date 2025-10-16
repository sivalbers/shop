<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthHelper;


class PunchOut extends Controller
{

    public function login($email, $password)
    {
        // Validierung
        $credentials = [
            'email'    => $email,
            'password' => $password
        ];


        // Versuch: Benutzer anmelden
        if (Auth::attempt($credentials)) {
            // Session regenerieren (sicherer gegen Session Fixation)
            session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login erfolgreich',
                'user'    => Auth::user(),
            ]);
        }

        // Wenn fehlgeschlagen:
        return response()->json([
            'success' => false,
            'message' => 'E-Mail oder Passwort ist falsch',
        ], 401);
    }

    public function handlePunchOutGet(Request $request)
    {
        // Hier kommt der PunchOut an.
        // Hier verarbeitest du den PunchOut-Request und erstellst die Antwort.
        // Beispiel: Prüfe die übermittelten Daten
        $data = $request->all();

        $action = $data['action'] ?? 'no action';
        $username= $data['Username'] ?? 'no user';
        $password= $data['Password'] ?? 'no password';
        $externalUserId= $data['externalUserId'] ?? 'no externalUserId';
        $target= $data['~TARGET'] ?? 'no target';
        $mercateoTarget= $data['mercateoTarget'] ?? 'no mercateoTarget';
        $hook_url= $data['HOOK_URL'] ?? 'no Hook_url';

        $userRepository = new UserRepository();
        $userDebitor = $userRepository->createPunchoutUser($data);

        Log::info('Get data:', [ $data, 'action' => $action, 'username' => $username, 'password' => $password,
                'externalUserId' => $externalUserId, 'target' => $target, 'mercateoTarget' => $mercateoTarget, 'hook_url' => $hook_url  ]);

        if (!$userDebitor){
            return response()->json([
                'success' => false,
                'message' => 'E-Mail oder Passwort ist falsch',
            ], 401);
        }
        else{
            $credentials = [
                'email'    => $externalUserId . '@'. $username . '.com',
                'password' => $password
            ];


            // Versuch: Benutzer anmelden
            if (Auth::attempt($credentials)) {
                // Session regenerieren (sicherer gegen Session Fixation)
                session()->regenerate();
                AuthHelper::AfterLogin($userDebitor);
                session()->put('hook_url', $hook_url );
                return redirect()->route('startseite');
            }
            else {
                session()->invalidate();
                session()->regenerateToken();
                session()->forget('hook_url');
                return response()->json([
                    'success' => false,
                    'message' => 'E-Mail oder Passwort ist falsch',
                ], 401);
            }


        }
    }

    public function handlePunchOutPost(Request $request): JsonResponse
    {

        // Hier verarbeitest du den PunchOut-Request und erstellst die Antwort.

        // Beispiel: Prüfe die übermittelten Daten
        $data = $request->all();

        Log::info('Post data:', [ $data ]);
        // Erstelle eine Beispielantwort
        /*
        $response = [
            'success' => true,
            'message' => 'PunchOut erfolgreich',
            'redirect_url' => 'http://shop.local/checkout',
        ];
        */


        return response()->json($data);
    }

    public static function sendToHookUrl(){
        $hook_url = session()->get('hook_url');
        $oci_cart_data = session()->get('oci_cart_data');

        \Log::info(['SendtoHookUrl' => $hook_url, 'oci_cart_data' => $oci_cart_data ]);

    }

public function submit()
{
    \Log::info('in Submit');

    // Prüfen ob die benötigten Session-Daten vorhanden sind
    if (!session()->has('hook_url')) {
        \Log::error('OCI Submit: hook_url fehlt in Session');
        return view('oci.error', [
            'message' => 'Fehler: Hook-URL nicht gefunden.'
        ]);
    }

    if (!session()->has('oci_cart_data')) {
        \Log::error('OCI Submit: oci_cart_data fehlt in Session');
        return view('oci.error', [
            'message' => 'Fehler: Warenkorb-Daten nicht gefunden.'
        ]);
    }

    $hookUrl = session('hook_url');
    $cartData = session('oci_cart_data');

    \Log::info('OCI Submit', [
        'hook_url' => $hookUrl,
        'items_count' => count($cartData['NEW_ITEM-DESCRIPTION'] ?? [])
    ]);

    // Session-Daten aufräumen (optional)
    session()->forget(['hook_url', 'oci_cart_data']);

    // View mit Auto-Submit-Formular zurückgeben
    // Das Formular sendet die Daten per POST an Mercateo
    return view('oci.submit', [
        'hookUrl' => $hookUrl,
        'cartData' => $cartData
    ]);
}


}
