<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nachricht extends Model
{
    use HasFactory;

    protected $table = 'nachrichten';

    protected $fillable = [
        'kurztext',
        'langtext',
        'von',
        'bis',
        'links',
        'prioritaet',
        'startseite',
        'kundennr',
        'mitlogin'
    ];

    // Optional: Füge eine Standardwertfunktion hinzu, falls gewünscht
    protected $attributes = [
        'prioritaet' => 'normal',
        'startseite' => false,
    ];
}
