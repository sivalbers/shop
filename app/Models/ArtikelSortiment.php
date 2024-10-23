<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtikelSortiment extends Model
{
    protected $table = 'artikel_sortimente';
    protected $primaryKey = ['artikelnr', 'sortiment'];  // Composite primary key
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'artikelnr',
        'sortiment',
    ];

    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'artikelnr', 'artikelnr');
    }

    public function sortiment()
    {
        return $this->belongsTo(Sortiment::class, 'sortiment', 'bezeichnung');
    }
}
