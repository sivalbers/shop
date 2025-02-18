<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Bestellung;
use App\Models\UserDebitor;

class AuthHelper
{
    /**
     * Vollständigen Benutzer-Logout durchführen:
     * - Auth::logout
     * - Session leeren
     * - CSRF-Token erneuern
     *
     * @param bool $flushSession Standard: true (komplette Session löschen)
     */
    public static function logoutUser(bool $flushSession = true): void
    {
        // 1️⃣ Authentifizierung beenden
        Auth::guard('web')->logout();

        // 2️⃣ Session-Daten löschen
        if ($flushSession) {
            Session::flush();           // ALLE Session-Daten löschen
        } else {
            Session::forget(['debitornr', 'sortiment']); // Nur bestimmte löschen
        }

        // 3️⃣ CSRF-Token erneuern
        Session::invalidate();
        Session::regenerateToken();

    }

    public static function logoutAndLogin(){
        AuthHelper::logoutUser();
        return redirect('/login')->with('message', 'Sie wurden erfolgreich abgemeldet.');
    }


    public static function AfterLogin(UserDebitor $stdDebitor){

        session()->put('debitornr', $stdDebitor->debitor_nr );
        session()->put('firma',     $stdDebitor->debitor->name);
        session()->put('sortiment', $stdDebitor->debitor->sortiment);
        session()->put('role',      $stdDebitor->role );

        $bestellung = Bestellung::getBasket();
        if ($bestellung){
            session()->put('bestellnr', $bestellung->nr);
            Log::info("Bestellummer", [session()->get('bestellnr')]);
        }
        else {
            dd("FEHLER: Kein Warenkorb erstellt!");
        }
    }
}
