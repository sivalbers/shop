<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationAuth extends Model
{
    use HasFactory;

    /**
     * Der Name der Tabelle, die mit diesem Model verbunden ist.
     *
     * @var string
     */
    protected $table = 'applicationauth';

    /**
     * Die Attribute, die massenzuweisbar sind.
     *
     * @var array
     */
    protected $fillable = [
        'applicationname',
        'apikey',
        'sessionid',
        'sessionexpiry',
        'lastlogin',
        'status',
        'allowedendpoints',
    ];

    /**
     * Die Attribute, die als Datumsobjekte behandelt werden sollen.
     *
     * @var array
     */
    protected $dates = [

        'lastlogin',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sessionexpiry' => 'datetime',
    ];

    /**
     * Die Standardwerte für die Attribute des Modells.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'inactive',
    ];

    /**
     * Konvertiere das Attribut "allowedendpoints" automatisch in ein Array (falls es JSON ist).
     *
     * @param string|null $value
     * @return array
     */
    public function getAllowedendpointsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Speichere das Attribut "allowedendpoints" als JSON.
     *
     * @param array|null $value
     * @return void
     */
    public function setAllowedendpointsAttribute($value)
    {
        $this->attributes['allowedendpoints'] = $value ? json_encode($value) : null;
    }
}
