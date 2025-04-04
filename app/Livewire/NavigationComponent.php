<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Livewire\Component;

use Illuminate\Support\Facades\auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

use App\Models\Bestellung;
use App\Models\UserDebitor;
use App\Models\Debitor;
use App\Models\Sortiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

class NavigationComponent extends Component
{
    public $kunden;
    public $bestellNr;
    public $countBasket;
    public $bestellung;
    public $name;
    public $sortiment;
    public $sortimentName;
    public $firma;
    public $debitornr;

    public $navText;

    public $anzpositionen;
    protected $listeners = ['updateAnzPositionen' => 'updateBestellungCount'];

    public function mount()
    {
        if (Auth::user()){
            $this->kunden = Auth::user()->debitoren;
            $this->sortiment = session()->get('sortiment');
            $this->sortimentName = Sortiment::getAnzeigeNamen($this->sortiment);
            $this->firma     = session()->get('firma');
            $this->debitornr = session()->get('debitornr');
    /*
            "<span style='font-size: 0.9em;'><span style='font-weight: bold; '>%s</span><br>
            <span style='font-weight: bold;'>%s</span><br>
            %s - <span style='font-weight: bold;'>%s</span></span>",
    */
            $this->navText = sprintf(

                "<span style='font-size: 0.9em;'>%s<br>
                %s<br>
                %s - %s</span>",
                Auth::user()->name,
                $this->firma,
                $this->debitornr,
                $this->sortimentName
            );



            $this->bestellung = Bestellung::getBasket();
            if($this->bestellung){
                $this->anzpositionen = $this->bestellung->anzpositionen;
            }
            else{
                $this->anzpositionen = 0;
            }
        }

    }

    public function render()
    {
        // Log::info('Navigation->render()',['bestellnr' => $this->bestellung->nr ]);
        return view('livewire.layout.navigation');
    }


    #[On('updateNavigation')]
    public function doUpdate()
    {
        $this->bestellung = Bestellung::getBasket();
        // Log::info('NavigationComponent->doUpdate()',['Bestellnr' => $this->bestellung->nr]);

        $this->bestellung = Bestellung::doCalc($this->bestellung->nr);
        $this->anzpositionen = $this->bestellung->anzpositionen;

        $this->dispatch('refresh-anzpositionen', ['detail' => $this->bestellung->anzpositionen]);


        // Log::info('NavigationComponent=>doUpdate', [ 'bestNr' => $this->bestellung->nr, 'anz' => $this->bestellung->anzpositionen, 'Gpreis'=> $this->bestellung->gesamtbetrag]);

    }

    #[On('updateAnzPositionen')]
    public function updateBestellungCount($count)
    {
        $this->anzpositionen = $count;
    }


    public function logout(Logout $logout): void
    {

        $logout();

        $this->redirect('/', navigate: true);
    }

    public function changeDebitor($debitorNr){
        $user_debitor = UserDebitor::where('debitor_nr', $debitorNr)->first();

        session()->put('debitornr', $user_debitor->debitor_nr );
        session()->put('firma',     $user_debitor->debitor->name);
        session()->put('sortiment', $user_debitor->debitor->sortiment);
        session()->put('rolle',     $user_debitor->rolle );

        $this->doUpdate();

        //Standard Debitor setzen
        UserDebitor::where('email', Auth::user()->email)->update(['standard' => 0]);
        $user_debitor->standard = 1;
        $user_debitor->save();


        $this->dispatch('page-reload');


    }


}
