<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ArtikelSortiment;
use App\Models\Artikel;
use App\Models\Sortiment;

class ArtikelSortimentComponent extends Component
{
    public $artikel_nr, $sortiment;
    public $updateMode = false;
    public $oldArtikelNr, $oldSortiment;

    public function render()
    {
        $artikelSortimente = ArtikelSortiment::all();
        $artikels = Artikel::all();
        $sortimente = Sortiment::all();
        return view('livewire.artikel-sortiment-component', compact('artikelSortimente', 'artikels', 'sortimente'));
    }

    public function resetInputFields()
    {
        $this->artikel_nr = '';
        $this->sortiment = '';
    }

    public function store()
    {
        $this->validate([
            'artikelnr' => 'required|string|max:20|exists:artikels,artikelnr',
            'sortiment' => 'required|string|max:20|exists:sortimente,bezeichnung',
        ]);

        ArtikelSortiment::create([
            'artikelnr' => $this->artikel_nr,
            'sortiment' => $this->sortiment,
        ]);

        session()->flash('message', 'Artikel-Sortiment erfolgreich hinzugefügt.');
        $this->resetInputFields();
    }

    public function edit($artikel_nr, $sortiment)
    {
        $artikelSortiment = ArtikelSortiment::where('artikelnr', $artikel_nr)
                                            ->where('sortiment', $sortiment)
                                            ->firstOrFail();
        $this->artikel_nr = $artikelSortiment->artikel_nr;
        $this->sortiment = $artikelSortiment->sortiment;
        $this->oldArtikelNr = $artikelSortiment->artikel_nr;
        $this->oldSortiment = $artikelSortiment->sortiment;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'artikelnr' => 'required|string|max:20|exists:artikels,artikelnr',
            'sortiment' => 'required|string|max:20|exists:sortimente,bezeichnung',
        ]);

        if ($this->oldArtikelNr && $this->oldSortiment) {
            $artikelSortiment = ArtikelSortiment::where('artikelnr', $this->oldArtikelNr)
                                                ->where('sortiment', $this->oldSortiment)
                                                ->firstOrFail();
            $artikelSortiment->update([
                'artikelnr' => $this->artikel_nr,
                'sortiment' => $this->sortiment,
            ]);

            $this->updateMode = false;
            session()->flash('message', 'Artikel-Sortiment erfolgreich aktualisiert.');
            $this->resetInputFields();
        }
    }

    public function delete($artikel_nr, $sortiment)
    {
        if ($artikel_nr && $sortiment) {
            ArtikelSortiment::where('artikelnr', $artikel_nr)
                            ->where('sortiment', $sortiment)
                            ->delete();
            session()->flash('message', 'Artikel-Sortiment erfolgreich gelöscht.');
        }
    }
}
