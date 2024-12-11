<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasOne;


use App\Models\Anschrift;


class Bestellung extends Model
{
    use HasFactory;

    protected $table = 'bestellungen'; // Der Tabellenname ist 'bestellung'

    protected $primaryKey = 'nr'; // Der Primärschlüssel ist 'nr'

    protected $fillable = [
        'datum',
        'kundennr',
        'user_id',
        'rechnungsadresse',
        'lieferadresse',
        'status_id',
        'gesamtbetrag',
        'anzpositionen',
        'kundenbestellnr',
        'kommission',
        'bemerkung',
        'lieferdatum',
    ];

    protected $casts= [
        'datum' => 'date',
        'kundennr' => 'integer',
        'user_id' => 'integer',
        'rechnungsadresse' => 'integer',
        'lieferadresse' => 'integer',
        'status_id' => 'integer',
        'gesamtbetrag'  => 'float',
        'anzpositionen' => 'integer',
        'lieferdatum' => 'date',
        'created_at' => 'date',
        'updated_at'  => 'date',
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
                ->where('status_id', 0)
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
                    'status_id' => 0,                  // Beispiel für einen Standardstatus (1 = 'offen' o.ä.)
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

    public static function doCalc($nr){


        DB::select('CALL GetBestellungSummary(?)', [$nr]);
        return Bestellung::where('nr', $nr)->first();
    }




}


