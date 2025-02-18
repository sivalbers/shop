<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Debitor;
use App\Models\UserDebitor;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function isAdmin(){
        return $this->role === 99;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $attributes = [
        'password' => '',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    public function userDebitors()
    {
        return $this->hasMany(UserDebitor::class, 'email', 'email');
    }

    public function standardDebitor()
    {
        return UserDebitor::where('email', $this->email )
            ->where('standard', 1)->first();

    }

    public function debitoren()
    {
        return $this->hasManyThrough(
            Debitor::class,        // Zielmodell (Debitor)
            UserDebitor::class,    // Zwischentabelle (Pivot)
            'email',               // Fremdschlüssel in UserDebitor (userdebitor.email)
            'nr',            // Fremdschlüssel in Debitor (debitor.kundennr)
            'email',               // Lokaler Schlüssel in User (users.email)
            'debitor_nr'           // Lokaler Schlüssel in UserDebitor (userdebitor.debitor_nr)
        );
    }
}
