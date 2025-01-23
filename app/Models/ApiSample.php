<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiSample extends Model
{
    // Tabelle, die von diesem Modell verwaltet wird
    protected $table = 'apisample';

    // Felder, die massenweise zugewiesen werden dürfen
    protected $fillable = [
        'bezeichnung',
        'url',
        'data',
    ];

    // Gebe an, dass das Feld `data` JSON-kodiert ist
    protected $casts = [
        'data' => 'array',
    ];
}
