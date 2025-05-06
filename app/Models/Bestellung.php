<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Helpers\AuthHelper;


use App\Models\Anschrift;
use App\Models\User;
use App\Models\BestellungPos;


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

    public function reAdresse()
    {
        return $this->belongsTo(Anschrift::class, 'rechnungsadresse', 'id');
    }

    public function lfAdresse()
    {
        return $this->belongsTo(Anschrift::class, 'lieferadresse', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id', 'status');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function positionen(){
        return $this->hasMany(BestellungPos::class, 'bestellnr', 'nr');
    }

    public static function getBasket(){
        $result = null;
        if( Auth::user() ){
            $user = Auth::user();
            $kundennr = Session()->get('debitornr');
            if (!$kundennr){
                AuthHelper::logoutUser();

                return redirect('/login')->with('message', 'Sie wurden erfolgreich abgemeldet.');

            }

            $bestellung = Bestellung::where('kundennr', $kundennr)
                ->where('user_id', $user->id)
                ->where('status_id', 0)
                ->first();

            if (!$bestellung){


                $LfAddr = Anschrift::Lieferadresse($kundennr);

                $ReAddr = Anschrift::Rechnungsadresse($kundennr);
                $bestellung = Bestellung::create([
                    'user_id' => $user->id,         // User-ID des angemeldeten Benutzers
                    'kundennr' => $kundennr,  // Kundennummer des angemeldeten Benutzers
                    'datum' => today(),
                    'rechnungsadresse' => $ReAddr,
                    'lieferadresse' => $LfAddr,
                    'status_id' => 0,                  // Beispiel für einen Standardstatus (1 = 'offen' o.ä.)
                    'gesamtbetrag' => 0.00,         // Standardwert für Gesamtbetrag (z.B. 0.00)0
                    'lieferdatum' => Bestellung::calcLFDatum(),
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

    public function lieferDatumStr(){
        return $this->lieferdatum ? $this->lieferdatum->format('d.m.Y') : '';

    }

    public static function calcLFDatum(){
        return Carbon::now()->addDays(4);
    }




}


