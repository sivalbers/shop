<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Nachricht extends Model
{
    use HasFactory;

    protected $table = 'nachrichten';

    protected $fillable = [
        'kurztext',
        'langtext',
        'von',
        'bis',
        'links',
        'prioritaet',
        'kopfzeile',
        'kundennr',
        'mitlogin',
        'mail'
    ];

    // Optional: Füge eine Standardwertfunktion hinzu, falls gewünscht
    protected $attributes = [
        'prioritaet' => 'normal',
        'kopfzeile' => false,
    ];

    protected $casts = [
        'von' => 'date',
        'bis' => 'date',
        'kopfzeile' => 'boolean',
        'mail' => 'boolean',
        'kundennr' => 'integer',
        'mitlogin' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function getVon_dmY(){
        return $this->von ? $this->von->format('d.m.Y') : '';
    }

    public function getVon_Ymd(){
        return $this->von ? $this->von->format('Y.m.d') : '';
    }

    public function getBis_dmY(){
        return $this->bis ? $this->bis->format('d.m.Y') : '';
    }
    public function getBis_Ymd(){
        return $this->bis ? $this->bis->format('Y.m.d') : '';
    }


    public function getVonBisStr(){
        $vonStr = $this->getVon_dmY();
        $bisStr = $this->getBis_dmY();

        if ($vonStr && $bisStr) {
            return "$vonStr - $bisStr";
        } elseif ($vonStr) {
            return $vonStr. " - ...";
        } elseif ($bisStr) {
            return "... - ".$bisStr;
        } else {
            return '... - ...';
        }

    }
    public function getLinksArray(){
        $arrayFormat = [];

        // Text in Zeilen aufteilen
        $zeilen = explode("\n", trim($this->links));

        // Jede Zeile parsen und in das Array-Format umwandeln
        foreach ($zeilen as $zeile) {
            if (strpos($zeile, '=>') !== false) {
                list($link, $beschreibung) = explode('=>', $zeile, 2);
                $arrayFormat[] = [
                    'link' => trim($link),
                    'beschreibung' => ($beschreibung != '') ? trim($beschreibung) : trim($link),
                ];
                Log::info('Link', [ 'link' => $link, 'Beschreibung' => $beschreibung]);
            }
            else
            {
                $arrayFormat[] = [
                    'link' => trim($zeile),
                    'beschreibung' => $zeile,
                ];
            }
        }
        return $arrayFormat;
    }

    public function isAbgelaufen(): bool
    {
        if (empty($this->bis)){
            return false ;
        }

//        dd($this->bis);
        return ($this->bis < now());


    }

}
