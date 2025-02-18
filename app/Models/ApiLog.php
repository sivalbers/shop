<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    /**
     * Die Tabelle, die mit dem Model verbunden ist.
     *
     * @var string
     */
    protected $table = 'apilogs';

    /**
     * Die Attribute, die massenweise zuweisbar sind.
     *
     * @var array
     */
    protected $fillable = [
        'version',
        'httpmethod',
        'pfad',
        'key',
        'session',
        'token',
        'data',
        'response',
    ];

}
