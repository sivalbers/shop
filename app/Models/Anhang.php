<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anhang extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anhaenge';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'artikelnr',
        'dateiname',
        'beschreibung',
        'art',
        'sort',
        'gesperrt',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'art' => 'integer',
        'sort' => 'integer',
        'gesperrt' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    /**
     * Get the article that owns the attachment.
     */
    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'artikelnr', 'artikelnr');
    }
}
