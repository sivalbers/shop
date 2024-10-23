<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Anschrift;

class Bestellung extends Model
{
    use HasFactory;

    protected $table = 'bestellung'; // Der Tabellenname ist 'bestellung'

    protected $primaryKey = 'nr'; // Der Primärschlüssel ist 'nr'

    protected $fillable = [
        'user_id',
        'datum',
        'kundennr',
        'rechnungsadresse',
        'lieferadresse',
        'status',
        'kundenbestellnr',
        'kommission',
        'bemerkung',
        'gesamtbetrag',
        'lieferdatum',
        'anzpositionen',
    ];

    public function rechnungsadresse()
    {
        return $this->belongsTo(Anschrift::class, 'id', 'rechnungsadresse');
    }

    public function lieferadresse()
    {
        return $this->belongsTo(Anschrift::class, 'id', 'lieferadresse' );
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id', 'status');
    }

    public static function getBasket(){
        $result = null;
        $user = Auth::user();
        if( $user ){
            $bestellung = Bestellung::where('kundennr', $user->kundennr)
                ->where('user_id', $user->id)
                ->where('status', 0)
                ->first();

            if (!$bestellung){


                $LfAddr = Anschrift::Lieferadresse($user->kundennr);

                $ReAddr = Anschrift::Rechnungsadresse($user->kundennr);
                $bestellung = Bestellung::create([
                    'user_id' => $user->id,         // User-ID des angemeldeten Benutzers
                    'kundennr' => $user->kundennr,  // Kundennummer des angemeldeten Benutzers
                    'datum' => today(),
                    'rechnungsadresse' => $ReAddr,
                    'lieferadresse' => $LfAddr,
                    'status' => 0,                  // Beispiel für einen Standardstatus (1 = 'offen' o.ä.)
                    'gesamtbetrag' => 0.00,         // Standardwert für Gesamtbetrag (z.B. 0.00)
                ]);
                $result = $bestellung ;
            }
            else{
                $result = $bestellung ;
            }
        }
        return $result ;

    }

    public function doCalc(){


        // Summe der gpreis-Felder
        $this->gesamtBetrag = Position::where('bestellnr', $this->nr)->sum('gpreis');

        // Anzahl der Positionen
        $this->anzpositionen = Position::where('bestellnr', $this->nr)->count('id');

        $this->save();

    }

}


