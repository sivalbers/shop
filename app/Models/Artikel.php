<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    protected $table = 'artikels';
    protected $primaryKey = 'artikelnr';
    public $incrementing = false;  // Da artikelnr kein Integer ist, aber ein Primary Key
    public $lagerbestand = 0 ;

    protected $fillable = [
        'artikelnr', 'bezeichnung', 'langtext', 'status', 'verpackungsmenge',
        'einheit', 'vkpreis', 'wgnr', 'steuer', 'bestand'
    ];

    public function warengruppe()
    {
       return $this->belongsTo(Warengruppe::class, 'wgnr', 'wgnr');
    }
}
