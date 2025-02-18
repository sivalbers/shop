<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRepository
{

    private string $logLevel;


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

    protected function validateRec($rec): bool
    {
        // Prüfen, ob `artikelnr` gesetzt und gültig ist
        if (!isset($rec->email) || !is_scalar($rec->email)) {
            $this->logMessage('warning', 'E-Mail ist ungültig oder fehlt.', ['artikelnr' => $rec->artikelnr]);
            return false;
        }
        if (!isset($rec->kundennr) || !is_scalar($rec->kundennr)) {
            $this->logMessage('warning', 'Kundennr ist ungültig oder fehlt.', ['artikelnr' => $rec->artikelnr]);
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

        try {
            $user = $this->updateUserFromData($user, $data);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren des Benutzers: ' . $e->getMessage(), ['data' => $data]);
            return false;
        }

        try {
            // Validierung des Datensatzes
            if (!$this->validateRec($user)) {
                return false;
            }

            if ($user->save()) {
                $this->logMessage('debug', 'Neue Benutzer-ID: ', ['userId' => $user->id]);
                return true;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            $this->logMessage('warning', 'Benutzer konnte nicht gespeichert werden.', ['data' => $data]);
            return false;

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


    function updateUserFromData($user, $data) {
        // Mapping der Spalten von `data` zu `Artikel`

        $mapping = [
            'customer_number'          => 'kundennr',
            'company'                  => 'name',
            'username'                 => 'login',
            'email'                    => 'email',
            'unlocked_product_ranges'  => 'sortiment',
         ];

        // Übertragen der Werte, falls vorhanden
        foreach ($mapping as $dataKey => $artikelKey) {
            if (isset($data[$dataKey])) {
                if (is_scalar($data[$dataKey])){
                    $user->$artikelKey = $data[$dataKey];
                }
                else {
                    try{
                    $user->$artikelKey =  implode(' ', $data[$dataKey]);
                    }
                    catch (\Throwable $e) {
                        $this->logMessage('error', "Fehler in Array '{ $data[$dataKey] }' fehlt. ", ['data' => $data[$dataKey]]);
                    }
                }
            } else {

                $this->logMessage('warning', "Datenfeld '{$dataKey}' fehlt. ", ['data' => $data]);
            }
        }

        return $user;

    }

}
