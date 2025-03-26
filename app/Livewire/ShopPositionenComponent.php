<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;


use App\Models\Bestellung;
use App\Models\BestellungPos;



class ShopPositionenComponent extends Component
{

    public $bestellung;

    public $listKurz = true;
    public $bPositionen;
    public $isPosModified = false ;

    /* Wird ausgeführt beim ersten aufruf. */
    public function mount(){

        $this->loadData();
    }


    /* Bestellung und Positionen laden */
    public function loadData(){

        $this->bestellung = Bestellung::getBasket();
        $mPositionen = BestellungPos::select('artikel.bezeichnung', 'artikel.langtext', 'bestellungen_pos.*',  'artikel.einheit')->where('bestellnr', $this->bestellung->nr)

            ->join('artikel', 'bestellungen_pos.artikelnr', '=', 'artikel.artikelnr')
            ->get();

        $this->bPositionen = array();

        foreach ($mPositionen as $position) {

            $this->bPositionen[$position->id] = [
                'menge'     => round($position->menge),
                'id'        => $position->id,
                'artikelnr' => $position->artikelnr,
                'bezeichnung' => $position->bezeichnung,
                'langtext' => $position->langtext,
                'epreis'    => $position->epreis,
                'gpreis'    => $position->gpreis,
                'einheit'   => $position->einheit,
                'steuer'    => $position->steuer,
                'bestand'   => $position->bestand,
            ];

        }
        $this->isPosModified = false ;
    }

    /* übergabe ins Frontend */
    public function render(){

        return view('livewire.shop.shopPositionen' );
    }


    function extractMiddleNumber($input) {
        // Verwende ein reguläres Muster, um die Zahl in der Mitte zu extrahieren
        if (preg_match('/bPositionen\.(\d+)\.menge/', $input, $matches)) {
            return $matches[1]; // Rückgabe der Zahl, die gefunden wurde
        }
        return null; // Rückgabe null, wenn keine Übereinstimmung gefunden wurde
    }

    function extractMiddleFld($input) {
        // Verwende ein reguläres Muster, um die Zahl in der Mitte zu extrahieren
        if (preg_match('/bPositionen\.(\d+)\.menge/', $input, $matches)) {
            return $matches[2]; // Rückgabe der Zahl, die gefunden wurde
        }
        return null; // Rückgabe null, wenn keine Übereinstimmung gefunden wurde
    }


    /*  Wird bei jeder Feldänderung die auf .live steht ausgeführt  */
    public function updated($fld){

        $this->isPosModified = true ;

        $this->validate( [
            $fld => 'required|integer|min:0',
        ]);

        $id = $this->extractMiddleNumber($fld);

        $this->bPositionen[$id]['gpreis'] = round($this->bPositionen[$id]['epreis']*$this->bPositionen[$id]['menge'],2);

        $this->calc();

    }


    /*  Berechnung des Zeilen und Gesamtpreises  */
    private function calc(){
        $gesamt = 0;
        foreach ($this->bPositionen as $key => $qu) {
            $gesamt = $gesamt + $qu['gpreis'];
            // $this->bPositionen[$key]['menge'] = $this->bPositionen[$key]['menge'];
        }
        $this->bestellung->gesamtbetrag = round($gesamt,2);
    }


    public function BtnCancel(){
        $this->updateFrontendPage();
    }


    /*Positionen speichern,
        Meldung an Bestellung, diese zu speichern */
    public function BtnSpeichern(){
        $gesamt = 0;
        $haveToUpdate = false ;
        foreach ($this->bPositionen as $key => $qu) {
            $pos = BestellungPos::where ('id', $this->bPositionen[$key]['id'])->first();
            if ($pos){
                if ($qu['menge'] > 0){
                    $pos->menge = $qu['menge'];
                    $pos->gpreis = round($pos->menge * $pos->epreis, 2);
                    $pos->save();
                    $gesamt = $gesamt + $pos->gpreis ;
                }
                else
                {
                    $pos->delete();
                    $haveToUpdate = true ;
                }
            }

        }
        if ($haveToUpdate){
            $this->bPositionen = array_filter($this->bPositionen, function ($qu) {
                return $qu['menge'] != 0;
            });


        }
        $this->bestellung->gesamtbetrag = round($gesamt,2);



        if (!$this->isPosModified && (count($this->bPositionen) > 0)){
            $this->dispatch('bestellungAbsenden'); // Warenkorbkomponent->bestellungAbsenden();
        }
        else {

            $this->dispatch('updateWarenkorb', doShowMessage: false ); // Warenkorbkomponent->updateWarenkorb();
            $this->dispatch('updateNavigation');
        }

        $this->isPosModified = false ;

    }

    /* Position löschen und Kopf neu berechnen */
    public function btnDelete($id){

        BestellungPos::where('id', $id)->delete();

        unset($this->bPositionen[$id]);

        $this->calc();
        $this->updateFrontendPage();
        //$this->dispatch('updateNavigation');

    }

    #[On('doRefreshPositionen')]
    public function doRefresh(){
        $this->bPositionen = array();
        $this->calc();
        $this->dispatch('updateNavigation');

    }


    /* updateNavigation => aktualisiert die Bestellung im Kopf
       refresh-page => lädt die gesamte Seite neu
            Die Javascript Postitionsmengen werden sonst nicht mit der Positions-Menge synchronisiert.
    */
    #[On('updatePosition')]
    public function updateFrontendPage(){

       $this->dispatch('refresh-page');
    }



}
