<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Artikel extends Model
{
    protected $table = 'artikel';
    protected $primaryKey = 'artikelnr';
    public $incrementing = false;  // Da artikelnr kein Integer ist, aber ein Primary Key
    public $lagerbestand = 0 ;

    protected $fillable = [
        'artikelnr', 'bezeichnung', 'langtext', 'verpackungsmenge',
        'einheit', 'vkpreis', 'steuer', 'bestand', 'wgnr', 'gesperrt'
    ];

    protected $casts = [
        'vkpreis' => 'float',
        'steuer' => 'float',
        'bestand' => 'float',
        'verpackungsmenge' => 'float',
        'gesperrt' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function warengruppe()
    {
       return $this->belongsTo(Warengruppe::class, 'wgnr', 'wgnr');
    }

    public function anhaenge()
    {
        return $this->hasMany(Anhang::class, 'artikelnr', 'artikelnr');
    }

    public function vkpreis()
    {
        $result = number_format($this->vkpreis, 2, ',', '.');
        return $result; // Ausgabe: 123.00
    }


    /* Getter und Setter */



}
