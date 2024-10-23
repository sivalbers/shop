<?php

namespace App\Livewire;

use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

use Livewire\Component;
use App\Models\Artikel;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class ArtikelComponent extends Component
{
    use WithPagination;

    public $artFilter = '';
    public $bezFilter = '';
    public $statusFilter = '';
    public $wgFilter = '';

    public function render()
    {
       // $this->resetPage();
      // Initialisiere den Query Builder
      $query = Artikel::query();

      // Filter nach Artikelnummer
      if (!empty($this->artFilter)) {
          $query->where('artikelnr', 'like', '%'.$this->artFilter.'%');
      }

      // Filter nach Bezeichnung
      if (!empty($this->bezFilter)) {
            $query->where('bezeichnung', 'like', '%'.$this->bezFilter.'%');
      }

      if (!empty($this->wgFilter)) {
          $query->where('wgnr', 'like', '%'.$this->wgFilter.'%');
      }


      // Filter nach Status
      if (!empty($this->statusFilter)) {
          $query->where('status', '=', $this->statusFilter);
      }

      // Paginierung der Ergebnisse und Rückgabe an die View
      $artikels = $query->paginate(30);

      return view('livewire.artikelliste', compact('artikels'));

    }


    #[On('applyArtikelFilter')]
    public function applyArtikelFilters($art, $bez, $stat)
    {
        $this->artFilter = $art;
        $this->bezFilter = $bez;
        $this->statusFilter = $stat;

        //dd($art);
        // Initialisiere den Query Builder

    }




}
