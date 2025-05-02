<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Nachricht;
use App\Models\UserNachrichtenStatus;
use App\Models\Config;


class UserNachrichten extends Component
{
    public $messageTime = 50;

    public $messages = [];


    public function mount(){

        $mt = Config::globalString('messageTime');
        if ($mt){
            $this->messageTime = (int)$mt;
        }

        $today = date('Y-m-d');

        $userId = Auth::id();

        $qu = Nachricht::whereIn('id', function ($query) use ($userId, $today) {
            $query->select('nachrichten_id')
                ->from('user_nachrichten_status')
                ->where('users_id', $userId)
                ->where('gelesen', 0)
                ->where(function ($q) use ($today) {
                    $q->where('von', '<=', $today)
                      ->orWhereNull('von');
                });
        })
        ->where(function ($query) use ($today) {
            $query->where('bis', '>=', $today)
                  ->orWhereNull('bis');
        })
        ->orderBy('created_at', 'asc')
        ->get();

        Log::info(['count' => $qu->count()]);
        foreach( $qu as $m){
            Log::info(['id' => $m->id]);
            $this->messages[] = [ 'id' => $m->id, 'ueberschrift' => $m->kurztext, 'text' => $m->langtext, 'prioritaet' => $m->prioritaet,
                'created_at' => $m->created_at->diffForHumans() ];
        }
    }

    public function render()
    {


        return view('livewire.user-nachrichten');
    }

    public function closeOverlay()
    {
        $this->messages = null;
    }

    public function markAsRead($nachricht){
//        Log::info(sprintf('Nachricht als gelesen markiert %d', $nachricht));


        UserNachrichtenStatus::markAsRead($nachricht);

        $this->dispatch('updateNavigation');

    }
}
