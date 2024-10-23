<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sortiment extends Model
{
    protected $table = 'sortimente';
    protected $primaryKey = 'bezeichnung';
    public $incrementing = false;

    protected $fillable = [
        'bezeichnung'
    ];
}
