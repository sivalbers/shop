<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WgHelper extends Model
{
    protected $table = 'wghelper';

    protected $fillable = [
        'wgnr',
        'sortiment',
    ];

}
