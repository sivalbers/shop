<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserNachrichtenStatus extends Model
{


    protected $table = 'user_nachrichten_status';


    protected $fillable = ['users_id', 'nachrichten_id', 'gelesen'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nachricht()
    {
        return $this->belongsTo(Nachricht::class);
    }

    public static function createNachricht($nachricht)
    {


        $users = \App\Models\User::all();


        foreach ($users as $user) {
            \App\Models\UserNachrichtenStatus::firstOrCreate(
                [
                    'users_id' => $user->id,
                    'nachrichten_id' => $nachricht->id,
                ],
                [
                    'gelesen' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }


    public static function markAsRead($nachrichten_id){

        $n = UserNachrichtenStatus::where('users_id', Auth::id())
            ->where('nachrichten_id', $nachrichten_id)
            ->first();
        if ($n) {
            $n->gelesen = 1;
            $n->save();
            return true;
        }
        return false;
    }


}
