<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status'; // Falls der Tabellenname anders als der Modelname ist

    protected $fillable = [
        'id',
        'bezeichnung', // Das Feld, das per Mass-Assignment befüllt werden darf
    ];
}
