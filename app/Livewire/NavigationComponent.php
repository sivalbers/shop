<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Livewire\Component;
use App\Models\Bestellung;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;


class NavigationComponent extends Component
{

    public $bestellNr;
    public $countBasket;
    public $bestellung;

    public function mount()
    {

        $this->bestellung = Bestellung::getBasket();

    }

    public function render()
    {

        return view('livewire.layout.navigation');
    }


    #[On('updateNavigation')]
    public function doUpdate()
    {
        $this->bestellung->doCalc();
        
    }


    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }


}
