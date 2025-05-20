<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZubehoerArtikel extends Model
{
    protected $table = 'zubehoerartikel';

    // Da kein Auto-Increment ID vorhanden ist
    public $incrementing = false;

    // Primary Key ist Composite → Laravel ignoriert das, also keine $primaryKey setzen
    protected $keyType = 'string';

    // Felder, die mass assignable sind (optional)
    protected $fillable = [
        'artikelnr',
        'zubehoerartikelnr',
    ];

    // Keine Standard-ID Spalte vorhanden
    public $timestamps = true;

    /*
     * Beziehung zum Original-Artikel
     */
    public function originalArtikel()
    {
        return $this->belongsTo(Artikel::class, 'artikelnr', 'artikelnr');
    }

    /*
     * Beziehung zum Ersatz-Artikel
     */
    public function zubehoerArtikel()
    {
        return $this->belongsTo(Artikel::class, 'zubehoerartikelnr', 'artikelnr');
    }
}
