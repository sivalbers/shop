<?php

namespace App\Livewire;

use Livewire\Component;

class SchnellerfassungComponent extends Component
{

    public $inText = '';
    public $outText = '';
    public $verarbeitet = false;
    public $artikel = [];
    public $sortiment;


    public function mount($sortiment)
    {
        $this->sortiment = $sortiment;
    }

    public function render()
    {
        return view('livewire.schnellerfassung');
    }

    public function verarbeiteText()
    {

        $cleaned = strip_tags($this->inText);
        $cleaned = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cleaned);
        $cleaned = preg_replace('/on\w+="[^"]*"/i', '', $cleaned);

        $this->artikel = $this->processInput($cleaned);

        $this->verarbeitet = true;

        // if (count($this->artikel) > 0 )
        {
            $this->dispatch('showArtikelSchnellerfassung', $this->artikel, $this->sortiment );
        }

    }

    function processInput($input)
    {
        // Spalten-Trennzeichen: Tab oder Semikolon
        $rows = preg_split('/\r\n|\r|\n/', $input); // Zeilen aufteilen
        $articles = [];

        foreach ($rows as $row) {
            // Spalten durch Tab oder Semikolon trennen
            $columns = preg_split('/[\t;,]/', $row);

            // Artikelnummern (6 bis 8 Stellen lang) und Mengen extrahieren
            $articleNumber = null;
            $quantity = null;
            $lastIx = -1;
            foreach ($columns as $ix => $column) {
                // Artikelnummer zwischen 6 und 8 Ziffern
                if (preg_match('/\b\d{6,8}\b/', $column, $matches)) {
                    $articleNumber = $matches[0];
                    $lastIx = $ix;
                }

                // Menge (ganzzahl)
                if (is_numeric($column) && intval($column) == $column) {
                    if ($lastIx != $ix){
                        $quantity = intval($column);
                    }
                }
            }

            // Wenn eine Artikelnummer vorhanden ist, aber keine Menge, setze die Menge auf 1
            if ($articleNumber && $quantity === null) {
                $quantity = 1;
            }

            // Wenn sowohl Artikelnummer als auch Menge gefunden wurden, speichern
            if ($articleNumber && $quantity) {
                $articles[] = [
                    'artikelnummer' => $articleNumber,
                    'menge' => $quantity,
                ];
            }
        }

        return $articles;
    }




}
