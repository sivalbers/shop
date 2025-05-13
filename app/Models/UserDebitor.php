<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserDebitor extends Model
{
    use HasFactory;

    protected $table = 'users_debitor';
    protected $fillable = [
        'email',
        'debitor_nr',
        'rolle',
    ];

    /**
     * Beziehung zum User (N:1).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Beziehung zum Debitor (N:1).
     */
    public function debitor()
    {
        return $this->belongsTo(Debitor::class, 'debitor_nr', 'nr');
    }


}
