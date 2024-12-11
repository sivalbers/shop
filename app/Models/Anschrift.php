<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Anschrift extends Model
{
    use HasFactory;

    protected $table = 'anschriften';

    protected $fillable = [
        'kundennr',
        'usersid',
        'kurzbeschreibung',
        'firma1',
        'firma2',
        'firma3',
        'strasse',
        'plz',
        'stadt',
        'land',
        'standard',
        'art'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'kundennr' => 'integer',
        'standard' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Beziehung zum Benutzer
    public function user()
    {
        return $this->belongsTo(User::class, 'kundennr', 'kundennr');
    }

    public static function Lieferadresse($kundennr){
        $lf = Anschrift::where('kundennr', $kundennr)
        ->where('art', 'Lieferadresse')
        ->where('standard', true)
        ->first();
        Log::info("1: ", [ $lf ] );
        if (!$lf) {
            $lf = Anschrift::where('kundennr', $kundennr)
            ->where('art', 'Lieferadresse')
            ->first();
            Log::info("2: ", [ $lf ] );
            if (!$lf) {
                $lf = Anschrift::where('kundennr', $kundennr)
                ->first();
                Log::info("3: ", [ $lf ] );
            }
        }
        Log::info("Result LF: ", [ $lf ] );
        $result = -1;
        if ($lf)
            $result = $lf->id;
        Log::info("Result LF-ID: ", [ $result ] );
        return $result;
    }

    public static function Rechnungsadresse($kundennr){
        $re = Anschrift::where('kundennr', $kundennr)
        ->where('art', 'Rechnungsadresse')
        ->where('standard', true)
        ->first();
        if (!$re) {
            $lf = Anschrift::where('kundennr', $kundennr)
            ->where('art', 'Rechnungsadresse')
            ->first();
            if (!$re) {
                $re = Anschrift::where('kundennr', $kundennr)
                ->first();
            }

        }
        Log::info("Result RE: ", [ $re ] );
        $result = -1;
        if ($re)
            $result = $re->id;
        Log::info("Result RE-ID: ", [ $result ] );
        return $result;
    }

    public static function getAdresseFormat($id){
        $adr = Anschrift::where('id', $id)->first();
        return $adr->format();
    }

    public function format(){
        // Array mit den Teilen der Adresse
        $parts = [
            $this->firma1 ? $this->firma1 . '<br>' : '',
            $this->firma2 ? $this->firma2 . '<br>' : '',
            $this->firma3 ? $this->firma3 . '<br>' : '',
            $this->strasse ? $this->strasse . '<br>' : '',
            ($this->land || $this->plz || $this->stadt) ? trim($this->land . '-' . $this->plz . ' ' . $this->stadt) : ''
        ];

        // Entferne leere Einträge aus dem Array
        $parts = array_filter($parts);

        // Füge die nicht leeren Teile zusammen
        $formattedString = implode("\n", $parts);

        return $formattedString;
    }


}
