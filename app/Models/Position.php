<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    // Definiere den Tabellennamen (falls nicht der Plural des Modells verwendet wird)
    protected $table = 'positionen';

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

    // Beziehung zu Bestellungen (1:n) - Eine Bestellung hat mehrere Positionen
    public function bestellung()
    {
        return $this->belongsTo(Bestellung::class, 'bestellnr', 'nr');
    }

    public static function getCount($bestellnr){

        return Position::where('bestellnr', $bestellnr)->count();
    }
}
