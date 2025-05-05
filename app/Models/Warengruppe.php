<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warengruppe extends Model
{
    protected $table = 'warengruppen';
    protected $primaryKey = 'wgnr';
    public $incrementing = false;  // Da 'wgnr' ein string primary key ist

    protected $fillable = [
        'wgnr',
        'bezeichnung',
    ];

    public function artikel()
    {
        return $this->hasMany(Artikel::class, 'wgnr', 'wgnr');
    }

    public static function getBezeichnung($wgNr){
        $mWg = Warengruppe::where('wgnr', $wgNr)->first();
        return $mWg->bezeichnung;
    }
}
