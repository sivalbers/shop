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

    // Rolle int
    // A = Admin
    // R = Reporter (Darf nachrichten erstellen)
    // S = Stammdatenmanager
    //                       S  R  A
    //              1 2 4 8 16 32 64


    public function isAdmin(){
        return ( $this->role & 64 ) === 64 ;
    }

    public function isStammdatenManager(){
        return (( $this->role & 16 ) === 16) | $this->isAdmin() ;
    }

    public function isReporter(){
        return (( $this->role & 32 ) === 32) | $this->isAdmin() ;
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
        $stdDeb = UserDebitor::where('email', $this->email )
            ->where('standard', 1)->first();
        if (empty($stdDeb)){
            $stdDeb = UserDebitor::where('email', $this->email )->first();
        }
        return $stdDeb;
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
