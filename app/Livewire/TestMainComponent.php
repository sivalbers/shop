<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class TestMainComponent extends Component
{

    public $isVerarbeitet = false;
    public $testText = 'TestTextMain';

    public function render()
    {
        Log::info('A TestMainComponent.render');
        return view('livewire.testmain');

    }

    public function verarbeite(){
        $this->isVerarbeitet = true ;

    }

    public function clear(){
        $this->isVerarbeitet = false ;
        $this->testText = '';
    }

    #[On('testMain')]
    public function testMain($wert){
        $this->isVerarbeitet = true ;
        $this->testText = 'Empfangen: '.$wert;
    }

    public function sendMainUnter(){
        $this->dispatch('testMainUnter', $this->testText);
    }

    public function sendMainRechts(){
        $this->dispatch('testMainRechts', $this->testText);
    }
}
