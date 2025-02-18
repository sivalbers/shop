<?php
namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Config extends Model
{
    use HasFactory;

    // Definiere den Tabellennamen
    protected $table = 'config';

    // Primärschlüssel-Feld
    protected $primaryKey = 'id';

    // Felder, die massenweise zugewiesen werden dürfen
    protected $fillable = [
        'kundennr',
        'userid',
        'option',
        'value',
        'json_data', // Da auch json_data verwendet wird
    ];

    public $timestamps = true;

    /**
     * Allgemeine Methode, um Daten basierend auf den Parametern abzurufen
     */
    public static function getConfigData($option, $kundennr = null, $userId = null, $type = 'value')
    {
        // Erstelle einen eindeutigen Cache-Schlüssel basierend auf den Parametern
        $cacheKey = "config_{$option}_{$kundennr}_{$userId}_{$type}";
        // Log::info('cacheKey', [ $cacheKey ]);

        // Lade die Daten aus dem Cache oder speichere sie für 60 Minuten
        $result = Cache::remember($cacheKey, 60, function () use ($option, $kundennr, $userId, $type) {
            // Führe die Query basierend auf den übergebenen Parametern aus
            $query = Config::where('option', $option);

            if ($kundennr !== null) {
                $query->where('kundennr', $kundennr);
            } else {
                $query->where('kundennr', null);
            }

            if ($userId !== null) {
                $query->where('userid', $userId);
            } else {
                $query->where('userid', null);
            }

            $md = $query->first();

            // Log::info('value', [ $md ? $md->value : 'Null' ]);

            // Rückgabe entweder 'value' oder 'json_data'
            return $md ? ($type === 'json' ? $md->json_data : $md->value) : '';
        });
        //Cache::forget($cacheKey);

        //Log::info("Get: ", [ $cacheKey, $result ]);
        return $result;
    }


    // Statische Hilfsmethoden für String und JSON
    public static function globalString($option)
    {
        return self::getConfigData($option);
    }

    public static function globalJson($option)
    {
        return self::getConfigData($option, null, null, 'json');
    }

    public static function kundennrString($option)
    {
        $kundennr = Session()->get('debitornr');
        return self::getConfigData($option, $kundennr);
    }

    public static function kundennrJson($option, $kundennr)
    {
        $kundennr = Session()->get('debitornr');
        return self::getConfigData($option, $kundennr, null, 'json');
    }

    public static function userString($option)
    {
        $kundennr = Session()->get('debitornr');
        $userId = Auth::User()->id;
        return self::getConfigData($option, $kundennr, $userId);
    }

    public static function userJson($option)
    {
        $kundennr = Session()->get('debitornr');
        $userId = Auth::User()->id;
        return self::getConfigData($option, $kundennr, $userId, 'json');
    }


    /**
     * Allgemeine Methode, um Daten basierend auf den Parametern zu speichern oder zu aktualisieren
     */
    public static function setConfigData($option, $kundennr = null, $userId = null, $value = null, $json_data = null)
    {
        $cacheKey = "config_{$option}_{$kundennr}_{$userId}_value";

        // Zuerst prüfen, ob der Datensatz bereits existiert
        $config = Config::where('option', $option)
            ->where(function ($query) use ($kundennr) {
                $query->where('kundennr', $kundennr)->orWhereNull('kundennr');
            })
            ->where(function ($query) use ($userId) {
                $query->where('userid', $userId)->orWhereNull('userid');
            })
            ->first();

        // Wenn der Datensatz existiert, aktualisieren, sonst einen neuen Datensatz erstellen
        if ($config) {
            $config->value = $value;
            $config->json_data = $json_data;
            $config->save();
        } else {
            Config::create([
                'option' => $option,
                'kundennr' => $kundennr,
                'userid' => $userId,
                'value' => $value,
                'json_data' => $json_data,
            ]);
        }

        // Den Cache aktualisieren
        Cache::forget($cacheKey);
        Cache::remember($cacheKey, 60, function () use ($value) {
            return $value;
        });
        // Log::info("Set: ", [ $cacheKey, $value ]);

        return true;
    }

    // Statische Hilfsmethoden für String und JSON - Schreiben
    public static function setGlobalString($option, $value)
    {
        return self::setConfigData($option, null, null, $value);
    }

    public static function setGlobalJson($option, $json_data)
    {
        return self::setConfigData($option, null, null, null, $json_data);
    }

    public static function setKundennrString($option, $value)
    {
        $kundennr = Session()->get('debitornr');
        return self::setConfigData($option, $kundennr, null, $value);
    }

    public static function setKundennrJson($option, $json_data)
    {
        $kundennr = Session()->get('debitornr');
        return self::setConfigData($option, $kundennr, null, null, $json_data);
    }

    public static function setUserString($option, $value)
    {
        $kundennr = Session()->get('debitornr');
        $userId = Auth::User()->id;

        return self::setConfigData($option, $kundennr, $userId, $value);
    }

    public static function setUserJson($option, $json_data)
    {
        $kundennr = Session()->get('debitornr');
        $userId = Auth::User()->id;
        return self::setConfigData($option, $kundennr, $userId, null, $json_data);
    }


}
