<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;


use App\Models\Bestellung;
use App\Models\Position;



class ShopPositionenComponent extends Component
{

    public $bestellung;

    public $listKurz = true;
    public $bPositionen;
    public $isPosModified = false ;

    /* Wird ausgeführt beim ersten aufruf. */
    public function mount(){
        Log::info('ShopPositionComponent.Mount()');
        $this->loadData();
    }


    /* Bestellung und Positionen laden */
    public function loadData(){
        Log::info('ShopPositionComponent.loadData()');
        $this->bestellung = Bestellung::getBasket();
        $mPositionen = Position::select('artikels.bezeichnung', 'artikels.langtext', 'positionen.*',  'artikels.einheit')->where('bestellnr', $this->bestellung->nr)

            ->join('artikels', 'positionen.artikelnr', '=', 'artikels.artikelnr')
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
                'bestand'   =>  rand(0, 10),
            ];

        }
        $this->isPosModified = false ;
    }

    /* übergabe ins Frontend */
    public function render(){
        Log::info('ShopPositionComponent.render()');

        return view('livewire.shop.shopPositionen' );
    }

    /* Menge aktualisieren
    #[On('updateQuantityPos')]
    public function updateQuantityPos($artikelnr, $quantity)
    {
        // Log::info('In updateQuantityPos', [ $artikelnr, $quantity ]);
        $this->isPosModified = true ;
        try{
            if ($quantity >= 0) {
                $this->bPositionen[$artikelnr]['menge'] = $quantity;
                //Log::info('Neue Menge ', [ $this->bPositionen[$artikelnr] ]);
                //$this->dispatch('updateQuantity' , $artikelnr, $quantity);
            }
        } catch (\Exception $e) {
            return log::error ( 'Fehler in updateQuantityPos: ' , [ $e->getMessage()]);
        }
    }
        */

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
        // Log::info('In updated');
        $this->isPosModified = true ;

            $this->validate( [
                $fld => 'required|integer|min:0',
            ]);

        // Log::info('updated',[$fld]);

        $id = $this->extractMiddleNumber($fld);

        $this->bPositionen[$id]['gpreis'] = round($this->bPositionen[$id]['epreis']*$this->bPositionen[$id]['menge'],2);
        // Log::info('Neue Preis',[$this->bPositionen[$id]['gpreis']]);

        $this->calc();

        // Log::info('Neue summe',[$this->bestellung->gesamtbetrag]);

    }


    /*  Berechnung des Zeilen und Gesamtpreises  */
    private function calc(){
        $gesamt = 0;
        foreach ($this->bPositionen as $key => $qu) {
            $gesamt = $gesamt + $qu['gpreis'];
            $this->bPositionen[$key]['menge'] = round($this->bPositionen[$key]['menge']);
        }
        $this->bestellung->gesamtbetrag = round($gesamt,2);
    }


    public function BtnCancel(){
        $this->updateFrontendPage();
    }


    /*Positionen speichern,
        Meldung an Bestellung, diese zu speichern */
    public function BtnSpeichern(){
        Log::info('In Speichern');

        foreach ($this->bPositionen as $key => $qu) {
            $pos = Position::where ('id', $this->bPositionen[$key]['id'])->first();
            if ($pos){
                $pos->menge = $qu['menge'];
                $pos->gpreis = round($pos->menge * $pos->epreis, 2);
                $pos->save();
            }
        }
        $this->dispatch('updateBestellung', [ 'updatePos' => true ]);
        $this->isPosModified = false ;
        //$this->updateFrontendPage();
    }

    /* Position löschen und Kopf neu berechnen */
    public function BtnDelete($id){

        Position::where('id', $id)->delete();

        unset($this->bPositionen[$id]);

        $this->calc();
        $this->dispatch('updateNavigation');
    }

    #[On('doRefreshPositionen')]
    public function doRefresh(){
        $this->bPositionen = array();
        $this->calc();
        $this->cancelReload();
    }


    /* updateNavigation => aktualisiert die Bestellung im Kopf
       refresh-page => lädt die gesamte Seite neu
            Die Javascript Postitionsmengen werden sonst nicht mit der Positions-Menge synchronisiert.
    */
    #[On('updatePosition')]
    public function updateFrontendPage(){
       $this->dispatch('updateNavigation');
       $this->dispatch('refresh-page');
    }

}
