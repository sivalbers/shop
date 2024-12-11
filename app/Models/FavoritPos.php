<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Favorit;

class FavoritPos extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'favoriten_pos';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'favoriten_id',
        'artikelnr',
        'sort',
        'menge',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'favoriten_id' => 'integer',
        'sort' => 'integer',
        'menge' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the favorite that owns the position.
     */
    public function favorite()
    {
        return $this->belongsTo(Favorit::class, 'favoriten_id');
    }


    public static function getFavoritenIDs($artikelnr){
        return FavoritPos::where('artikelnr', $artikelnr)->pluck('favoriten_id')->toArray();
    }

}
