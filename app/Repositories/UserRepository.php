<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Debitor;
use App\Models\UserDebitor;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Anschrift;

class UserRepository
{

    private string $logLevel;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = config('logging.user_repository_log_level', 'error');
        $this->logMessage('debug', 'Test muss geloggt werden.');
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

    protected function validateUser($rec): bool{
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->email) || !is_scalar($rec->email)) {
            $this->logMessage('warning', 'E-Mail ist ungültig oder fehlt.', ['email' => $rec->email]);
            return false;
        }

        return true;
    }

    protected function validateUserDebitor($rec): bool{
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->email) || !is_scalar($rec->email)) {
            $this->logMessage('warning', 'E-Mail ist ungültig oder fehlt.', ['email' => $rec->email]);
            return false;
        }
        if (!isset($rec->debitor_nr) || !is_scalar($rec->debitor_nr)) {
            $this->logMessage('warning', 'Debitor_nr ist ungültig oder fehlt.', ['debitor_nr' => $rec->debitor_nr]);
            return false;
        }

        return true;
    }

    protected function validateDebitor($rec): bool{
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->nr) || !is_scalar($rec->nr)) {
            $this->logMessage('warning', 'Nr ist ungültig oder fehlt.', ['nr' => $rec->nr]);
            return false;
        }
        if (!isset($rec->sortiment) || !is_scalar($rec->sortiment)) {
            $this->logMessage('warning', 'Sortiment ist ungültig oder fehlt.', ['sortiment' => $rec->sortiment]);
            return false;
        }

        return true;
    }


    public function getById($id)
    {
        try{
            return User::findOrFail($id);
        } catch (\Exception $e) {
            $this->logMessage('error', 'User konnte nicht geladen werden', [ 'id' => $id, $e->getMessage()]);
        } finally {
            $this->logMessage('info', 'User gelesen', [ 'id' => $id ]);
            return -1;
        }
    }

    public function getAll()
    {
        return User::all();
    }

    public function create(array $data){
        $result = 0 ;
        $user = new User();
        $debitor = new Debitor();
        $userDebitor = new UserDebitor();
        try {
            $this->updateUserFromData($user, $debitor, $userDebitor, $data);

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des Benutzers: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            // Validierung des Datensatzes
            if (!$this->validateUser($user)) {
                $this->logMessage('error', 'E-Mail-Adresse nicht vorhanden:', [ 'data' => $data]);
                return false;
            }
            else {
                Log::info('Prüfung User => Okay.');
            }

            if (!$this->validateDebitor($debitor)) {
                return false;
            }
            else {
                Log::info('Prüfung Debitor => Okay.');
            }

            if (!$this->validateUserDebitor($userDebitor)) {
                return false;
            }
            else {
                Log::info('Prüfung user_debitor => Okay.');
            }

            // Log::info([ 'Suche nach Users über E-Mail: ' => $user->email]);

            $found = User::where('email', $user->email)->exists();

            // Log::info(['User Found' => $found, 'email' => $user->email]);

            if ($found === false) {
                $user->save();
                // $result = $user->id; Nicht user sondern debitor-Nr zrückgeben
                $this->logMessage('debug', 'Neue Benutzer-ID: ', ['userId' => $user->id]);
            }
            else{
                $u = User::where('email', $user->email)->first();
                // $result = $u->id; Nicht user sondern debitor-Nr zrückgeben
                Log::info('Benutzer existiert. Es wurde kein neuer Benutzer angelegt');
            }

            //  Log::info([ 'Suche nach Debitor über Nr: ' => $debitor->nr ]);
            $found = Debitor::where('nr', $debitor->nr)->exists();
            if ($found === false) {
                $debitor->save();
                $this->logMessage('debug', 'Neue debitor-ID: ', ['debitor->nr' => $debitor->nr]);

            }
            else{

                Log::info('Debitor existiert. Es wurde kein neuer Debitor angelegt');
            }


            $found = UserDebitor::where('email', $user->email)->where('debitor_nr', $debitor->nr)->exists();
            if ($found === false) {
                $userDebitor->save();
                $this->logMessage('debug', 'Neue UserDebitor-ID: ', ['userDebitor->id' => $userDebitor->id]);
            }
            else{
                Log::info('User_Debitor existiert. Es wurde kein neuer UserDebitor angelegt');
            }

            $result = $debitor->nr;

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            // $this->logMessage('warning', 'Benutzer konnte nicht gespeichert werden.', ['data' => $data]);
            return $result;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Create: Fehler beim Speichern des Benutzers: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    protected function validateRec($rec): bool
    {
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->name) || !is_scalar($rec->name)) {
            $this->logMessage('error', 'name ist ungültig oder fehlt.', ['name' => $rec->name]);
            return false;
        }

        if (empty($rec->email) || !is_scalar($rec->email)) {
            $this->logMessage('error', 'email ist ungültig oder fehlt.', ['email' => $rec->email ?? null]);
            return false;
        }

        return true;
    }

    public function update($id, array $data)
    {
        Log::info('user update');
        $user = new User();
        $debitor = Debitor::where('nr' , $id)->first();

        $debitor->nr = $id;

        $userDebitor = new UserDebitor();


        try {
            $this->updateUserFromData($user, $debitor, $userDebitor, $data);

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des Benutzers: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        if (!$this->validateDebitor($debitor)) {
            return false;

        }
        else {
            $debitor->save();
            Log::info('Prüfung Debitor => Okay.');
        }

        Log::info([ 'nr' => $debitor->nr, 'name' => $debitor->name, 'E-mail' => $user->email ]);
        $this->handleStdEmailAdress($debitor->nr, $debitor->name, $user->email);






        return $id;

    }


    public function delete($id) {
        try{
            $user = User::findOrFail($id);
            $user->delete();
            return true;
        }
        catch(\Exception $e){
            return true;
        }
    }


    function updateUserFromData($user, $debitor, $userDebitor, $data) {    // Mapping der Spalten von `data` zu `Users`
        $this->logMessage('debug', 'UpdateUserFromData', [ 'data' => $data]);
        $mapping = [
            'customer_number'          => 'kundennr', // $debitor->nr, $userDebitor->debitor_nr
            'company'                  => 'name',     // $debitor->name
            'email'                    => 'email',    // $user->email, $userDebitor->email
            'unlocked_product_ranges'  => 'sortiment', // array ["BE", "TK" ] => $debitor->sortiment = "BE TK"
         ];

        if (isset($data['customer_number'])){
            $debitor->nr                = $data['customer_number'];
        }

        if (isset($data['company'])){
            $debitor->name              = trim($data['company']);
            $user->name                 = $debitor->name;
        }
        else
            if (isset($data['firstname'])){
                $debitor->name           = trim($data['firstname']);
                $user->name              = $debitor->name;
            }


        $userDebitor->debitor_nr    = $debitor->nr;

        if (isset($data['email'])){
            $user->email           = $data['email'];
            $userDebitor->email    = $user->email;
        }

        if (isset($data['unlocked_product_ranges'])){
            $debitor->sortiment         = implode(' ', $data['unlocked_product_ranges']);
        }

        return true;

    }



    function handleStdEmailAdress($debitorNr, $name, $email){

        $user = User::where('email', $email)->first();

        if (empty($user)) {

            $user = new user();
            $user->name = $name;
            $user->email = $email;
            $user->role = 64;

            $user->save();

            $this->logMessage('debug', 'Neue Benutzer-ID: ', ['userId' => $user->id]);

            UserDebitor::where('debitor_nr', $debitorNr)
                ->where('email', '!=', $email)
                ->update([
                    'rolle' => 0,
                    'standard' => 0,
                ]);

            $userDebitor = new UserDebitor();
            $userDebitor->email = $email;
            $userDebitor->debitor_nr = $debitorNr;
            $userDebitor->rolle = 1;
            $userDebitor->standard = 1;
            $userDebitor->save();

        }

    }


    public function createPunchoutUser(array $data){

        // $data auslesen
        $username= $data['Username'] ?? 'no user';  // mercateo oder wesernetz
        $password= $data['Password'] ?? 'no password';
        $externalUserId= $data['externalUserId'] ?? 'no externalUserId'; // z.B. samundt

        $email = $externalUserId . '@'. $username . '.com'; // samundt@wesrnetz.com

        Log::info([ '$email' => $email ] );
        // Prüfen, ob Benutzer existiert, wenn nicht anlegen
        $user = User::where('email', $email)->first();
        if (empty($user)) {
            $user = new User();
            $user->name = $externalUserId;
            $user->email = $email;
            $user->role = 8; // PunchOut User
            $user->password = bcrypt($password);
            $user->save();

            $this->logMessage('debug', 'Neue Benutzer-ID: ', ['userId' => $user->id]);
        }
        else{
            Log::info('Benutzer existiert. Es wurde kein neuer Benutzer angelegt');
        }

        // Prüfen, ob Debitor existiert, wenn nicht anlegen
        $debitor = Debitor::where('name', $username)->first();
        if (empty($debitor)) {
            $debitor = new Debitor();
            if ($username = 'mercateo'){
                $debitor->nr = 21011;
                $debitor->name = 'mercateo';
                $debitor->sortiment = 'EWE TK';
                $debitor->abholer = 0;
                $debitor->save();
            }
            elseif ($username = 'wesernetz'){
                $debitor->nr = 26020;
                $debitor->name = 'wesernetz';
                $debitor->sortiment = 'BE WN';
                $debitor->abholer = 0;
                $debitor->save();
            }
        }
        $userDebitor = UserDebitor::where('email', $email)->where('debitor_nr', $debitor->nr)->with('debitor')->first();
        if (empty($userDebitor)) {
            $userDebitor = new UserDebitor();
            $userDebitor->email = $email;
            $userDebitor->debitor_nr = $debitor->nr;
            $userDebitor->rolle = 0;
            $userDebitor->standard = 1;
            $userDebitor->save();
        }


        $userDebitor = UserDebitor::where('email', $email)->where('debitor_nr', $debitor->nr)->with('debitor')->first();


        $ans = AnschriftRepository::checkPunchOutAnschrift($debitor);

        if ($ans !== true ){
            Log::info('Keine Anschrift gefunden, FEHLER!');
        }


        return $userDebitor;
    }


}
