<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Sortiment extends Model
{
    protected $table = 'sortimente';
    protected $primaryKey = 'bezeichnung';
    public $incrementing = false;

    protected $fillable = [
        'bezeichnung',
        'anzeigename'
    ];

    public static function getAnzeigeName($sortiment){
        try{
        return Sortiment::where('bezeichnung', $sortiment)->pluck('anzeigename')[0];
        }
        catch(\Exception $e){
            Log::error(sprintf('%s hat keinen anzeigenamen: Fehler: %s', $sortiment, $e));
        }
    }


    public static function getAnzeigeNamen($sortiment)
    {
        $arr = explode(" ", $sortiment);
        $s = "";
        foreach($arr as $sortiment){
            $name = Sortiment::getAnzeigeName($sortiment);
            $s = sprintf("%s %s",$s, $name);
        }
        return $s;
    }
}
