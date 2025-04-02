<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debitor extends Model
{
    use HasFactory;

    protected $table = 'debitor';
    protected $primaryKey = 'nr';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'sortiment',
        'gesperrt',
    ];

    /**
     * Beziehung zu UserDebitoren (1:N).
     */
    public function userDebitors()
    {
        return $this->hasMany(UserDebitor::class, 'debitor_nr', 'nr');
    }

    public function sortimentName()
    {
        $arr = explode(" ", $this->sortiment);
        $s = "";
        foreach($arr as $sortiment){
            $name = Sortiment::getAnzeigeName($sortiment);
            $s = sprintf("%s %s",$s, $name);
        }
        return $s;
    }


}
