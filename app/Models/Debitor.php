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
        $namen = [];

        foreach ($arr as $sortiment) {
            $namen[] = Sortiment::getAnzeigeName($sortiment);
        }

        return implode(' | ', $namen);
    }


}
