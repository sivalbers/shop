<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\FavoritPos;

class Favorit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'favoriten';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kundennr',
        'user_id',
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'kundennr' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function positions()
    {
        return $this->hasMany(FavoritPos::class, 'favoriten_id');
    }

    public static function favoritenStr(){
        return 'favoriten_' . Auth::user()->kundennr . ' '.Auth::user()->id;
    }

    protected static function boot()
    {
        parent::boot();

        // Event: Nach dem Speichern
        static::saved(function () {
            Cache::forget(Favorit::favoritenStr());
            Favorit::cFavoriten(); // Cache aktualisieren
            // Log::info('Favorit wurde gespeichert. Cache aktualisiert.');
        });

        // Event: Nach dem Löschen
        static::deleted(function () {
            Cache::forget(Favorit::favoritenStr());
            Favorit::cFavoriten(); // Cache aktualisieren
            // Log::info('Favorit wurde gelöscht. Cache aktualisiert.');
        });
    }

    public static function cFavoriten($cRefresh = false)
    {
        //Log::info('cRefresh Wert: ' . json_encode($cRefresh));
        //Log::info('Cache vorhanden: ' . (Cache::has('favoriten') ? 'Ja' : 'Nein'));


        if ($cRefresh === true || !Cache::has(Favorit::favoritenStr()))
        {
            /*
            $favoriten = Favorit::where('kundennr', Auth::user()->kundennr)->get(['id', 'name', 'user_id'])
                ->keyBy('id')
                ->toArray();
            */
            $userID = Auth::id(); // Die Benutzer-ID des aktuell angemeldeten Nutzers
            $kundennr = Auth::user()->kundennr; // Kundennummer des angemeldeten Nutzers
            $fav = Favorit::where('kundennr', $kundennr)
                ->where(function ($query) use ($userID) {
                    $query->where('user_id', 0)
                          ->orWhere('user_id', $userID);
                });

            // Log::info([ 'favoriten' => $fav->toRawSql()]);

            $favoriten = $fav->get(['id', 'name', 'user_id'])
                ->keyBy('id')
                ->toArray();
            // Log::info(var_dump($favoriten));
            // Log::info('Favorit Cache wird entfernt.');
            Cache::forget(Favorit::favoritenStr());
            // Log::info('Favorit Cache wird zugefügt.');
            Cache::put(Favorit::favoritenStr(), $favoriten, now()->addHours(1)); // Cache für 1 Stunde
            // Log::info('Favorit Cache wurde aktualisiert.');
        }
        // else
        {
            // Log::info('Favorit Cache NICHT aktualisiert.');
        }

        return Cache::get(Favorit::favoritenStr());
    }

}
