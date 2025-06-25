<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Debitor;
use App\Models\UserDebitor;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRepository
{

    private string $logLevel;
    private Debitor $debitor;
    private UserDebitor $userDebitor;


//#REGION Logging
    public function __construct()
    {
        // Lade Log-Level aus der Konfiguration (z. B. aus der .env über logging.php)
        $this->logLevel = config('logging.user_repository_log_level', 'error');
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
                return false;
            }
            else {
                Log::info('validateUser = gültig.');
            }
            if (!$this->validateUserDebitor($userDebitor)) {
                return false;
            }
            else {
                Log::info('validateUserDebitor = gültig.');
            }

            if (!$this->validateDebitor($debitor)) {
                return false;
            }
            else {
                Log::info('validateDebitor = gültig.');
            }

            Log::info([ 'Suche nach E-Mail' => $user->email]);

            $found = User::where('email', $user->email)->exists();

            Log::info(['User Found' => $found, 'email' => $user->email]);

            if ($found === false) {
                $user->save();
                $this->logMessage('debug', 'Neue Benutzer-ID: ', ['userId' => $user->id]);

            }
            else{
                Log::info('Benutzer existiert. Es wurde kein neuer Benutzer angelegt');
            }


            $found = Debitor::where('nr', $debitor->nr)->exists();
            if ($found === false) {
                $debitor->save();
                $this->logMessage('debug', 'Neue debitor-ID: ', ['debitor->nr' => $debitor->nr]);
                return $debitor->nr;
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

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            // $this->logMessage('warning', 'Benutzer konnte nicht gespeichert werden.', ['data' => $data]);
            return $debitor->nr;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Create: Fehler beim Speichern des Benutzers: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }

    public function update($id, array $data)
    {
        try {
            $user = User::findOrFail($id);

        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Update:: Benutzer nicht gefunden: ' . $e->getMessage(), ['artikelnr' => $id]);
            return false;
        }

        try {
            $user = $this->updateUserFromData($user, $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des Benutzers: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            return ($this->validateRec($user) && $user->save());

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            Log::warning('Benutzer konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

        } catch (\Exception $e) {
            Log::error('Update: Fehler beim Speichern des Artikels: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }
    }


    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }


    function updateUserFromData(&$user, &$debitor, &$userDebitor, $data) {
        // Mapping der Spalten von `data` zu `Users`

        $mapping = [
            'customer_number'          => 'kundennr', // $debitor->nr, $userDebitor->debitor_nr
            'company'                  => 'name',     // $debitor->name
            'email'                    => 'email',    // $user->email, $userDebitor->email
            'unlocked_product_ranges'  => 'sortiment', // array ["BE", "TK" ] => $debitor->sortiment = "BE TK"
         ];

        $debitor->nr                = $data['customer_number'];
        $userDebitor->debitor_nr    = $debitor->nr;

        $debitor->name              = $data['company'];
        $user->name                 = $debitor->name;
        $user->email                = $data['email'];
        $userDebitor->email         = $user->email;
        $debitor->sortiment         = implode(' ', $data['unlocked_product_ranges']);

        return true;

    }

}
