<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Livewire\Component;
use App\Models\Bestellung;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;


class NavigationComponent extends Component
{

    public $bestellNr;
    public $countBasket;
    public $bestellung;

    public function mount()
    {

        Log::info('Navigation->mount()');
        $this->bestellung = Bestellung::getBasket();

    }

    public function render()
    {
        Log::info('Navigation->render()',['bestellnr' => $this->bestellung->nr ]);
        return view('livewire.layout.navigation');
    }


    #[On('updateNavigation')]
    public function doUpdate()
    {
        $this->bestellung = Bestellung::getBasket();
        Log::info('NavigationComponent->doUpdate()',['Bestellnr' => $this->bestellung->nr]);
        $this->bestellung = Bestellung::doCalc($this->bestellung->nr);

        Log::info('NavigationComponent=>doUpdate', [ 'bestNr' => $this->bestellung->nr, 'anz' => $this->bestellung->anzpositionen, 'Gpreis'=> $this->bestellung->gesamtbetrag]);

    }


    public function logout(Logout $logout): void
    {

        $logout();

        $this->redirect('/', navigate: true);
    }


}
