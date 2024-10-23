<?php

namespace App\Livewire;


use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class TestMainUnterComponent extends Component
{

    public $isVerarbeitet = false;
    public $testText = 'TestTextMainUnter';

    public function render()
    {

        Log::info('A TestMainUnterComponent.render');
        return view('livewire.testmainunter');

    }

    public function clear(){
        $this->isVerarbeitet = false ;
        $this->testText = '';
    }

    public function verarbeite(){
        $this->isVerarbeitet = true ;

    }

    #[On('testMainUnter')]
    public function testMainUnter($wert){
        $this->isVerarbeitet = true ;
        $this->testText = 'Empfangen: '.$wert;
    }

    public function sendMain(){
        $this->dispatch('testMain', $this->testText);
    }

    public function sendMainRechts(){
        $this->dispatch('testMainRechts', $this->testText);
    }


}
