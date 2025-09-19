<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Artikel;
use App\Models\ArtikelSortiment;
use Illuminate\Support\Facades\Log;


class ArtikelSuche extends Component
{
    public string $suchbegriff = '';
    public array $ergebnisse = [];

    public function updatedSuchbegriff()
    {
        if ($this->suchbegriff != '') {

            $sortiment = explode(' ', session()->get('sortiment'));
            // Log::info(sprintf('Suchbegriff: %s Sortiment %s', $this->suchbegriff, session()->get('sortiment')));

            $artikelBezArr = explode(' ', $this->suchbegriff);

            $query = Artikel::query();

            if (!empty($artikelBezArr)) {
                foreach ($artikelBezArr as $part) {
                    $query->where(function ($q) use ($part) {
                        $q->where('artikelnr', 'like', "%{$part}%")
                        ->orwhere('bezeichnung', 'like', "%{$part}%")
                        ->orWhere('langtext', 'like', "%{$part}%");
                    });
                }
            }

            $this->ergebnisse = $query->whereIn('artikelnr', ArtikelSortiment::whereIn('sortiment', $sortiment)->pluck('artikelnr'))
            ->limit(100)
            ->get()
            ->toArray();

        }
        else
            $this->ergebnisse = [];

            Log::info(sprintf('Suchbegriff: %s %d', 'Fertig', count($this->ergebnisse)));

    }

    public function render()
    {
        return view('livewire.artikel-suche');
    }
}
