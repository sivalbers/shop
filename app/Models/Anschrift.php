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

        if (!$lf) {
            $lf = Anschrift::where('kundennr', $kundennr)
            ->where('art', 'Lieferadresse')
            ->first();

            if (!$lf) {
                $lf = Anschrift::where('kundennr', $kundennr)
                ->where('art', '')
                ->first();

            }
        }

        $result = -1;
        if ($lf)
            $result = $lf->id;

        return $result;
    }

    public static function Rechnungsadresse($kundennr){
        $result = -1;

        $re = Anschrift::where('kundennr', $kundennr)
        ->where('art', 'Rechnungsadresse')
        ->where('standard', true)
        ->first();

        if (empty($re)) {
            // Standard Rechnungsanschrift nicht gefunden
            $re = Anschrift::where('kundennr', $kundennr)
            ->where('art', 'Rechnungsadresse')
            ->first();

            if (empty($re)) {
                // Keine Rechnungsanschrift gefunden, versuche neutrale Adresse
                $re = Anschrift::where('kundennr', $kundennr)
                ->where('art', '')
                ->first();
            }

        }

        if (!empty($re)){
            $result = $re->id;
        }



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
