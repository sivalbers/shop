<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class TestMainRechtsComponent extends Component
{

    public $isVerarbeitet = false;
    public $testText = 'TestTextMainRechts';

    public function render()
    {

        Log::info('A TestMainRechtsComponent.render');
        return view('livewire.testmainrechts');

    }

    public function clear(){
        $this->isVerarbeitet = false ;
        $this->testText = '';
    }

    public function verarbeite(){
        $this->isVerarbeitet = true ;

    }

    #[On('testMainRechts')]
    public function testMainRechts($wert){
        $this->isVerarbeitet = true ;
        $this->testText = 'Empfangen: '.$wert;
    }

    public function sendMain(){
        $this->dispatch('testMain', $this->testText);
    }

    public function sendMainUnter(){
        $this->dispatch('testMainUnter', $this->testText);
    }

}
