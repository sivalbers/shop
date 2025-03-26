<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BestellungPos extends Model
{
    use HasFactory;


    // Definiere den Tabellennamen (falls nicht der Plural des Modells verwendet wird)
    protected $table = 'bestellungen_pos';

    // Definiere die Felder, die massenweise zugewiesen werden dürfen
    protected $fillable = [
        'bestellnr',
        'artikelnr',
        'menge',
        'epreis',
        'gpreis',
        'steuer',
        'sort'
    ];

    protected $casts= [

        'bestellnr' => 'integer',
        'menge' => 'integer',
        'epreis' => 'float',
        'gpreis' => 'float',
        'steuer'  => 'float',
        'sort' => 'integer',
        'created_at' => 'date',
        'updated_at'  => 'date',
    ];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bestellungPos) {
            // Log::info('BestellungPos->boot->saving: ',[ 'menge' => $bestellungPos->menge, 'epreis' => $bestellungPos->epreis, 'gpreis' => ($bestellungPos->menge * $bestellungPos->epreis)]);
            $bestellungPos->gpreis = $bestellungPos->menge * ($bestellungPos->epreis );
        });
    }

    // Beziehung zu Bestellungen (1:n) - Eine Bestellung hat mehrere Positionen
    public function bestellung()
    {
        return $this->belongsTo(Bestellung::class, 'bestellnr', 'nr');
    }

    public static function getCount($bestellnr){

        return BestellungPos::where('bestellnr', $bestellnr)->count();
    }

    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'artikelnr', 'artikelnr' );
    }



}
