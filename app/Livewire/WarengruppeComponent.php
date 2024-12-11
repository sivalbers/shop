<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Warengruppe;

class WarengruppeComponent extends Component
{
    use WithPagination;

    public $wgnr, $bezeichnung;
    public $updateMode = false;
    public $oldWgnr;
    public $wgFilter = '';
    public $bezFilter = '';

    public function render()
    {

        $abfrage = Warengruppe::query();


        if ($this->wgFilter != '') {
            $abfrage->where('wgnr', 'like', '%'.$this->wgFilter.'%');
        }

        if ($this->bezFilter != '') {
            $abfrage->where('bezeichnung', 'like', '%'.$this->bezFilter.'%');
        }
        Log::info($abfrage->toSQL());
        $warengruppen = $abfrage->paginate(30);

        return view('livewire.warengruppe-component', compact('warengruppen'));
    }

    public function resetInputFields()
    {
        $this->wgnr = '';
        $this->bezeichnung = '';
    }

    public function store()
    {
        $this->validate([
            'wgnr' => 'required|string|max:20|unique:warengruppen,wgnr',
            'bezeichnung' => 'required|string|max:80',
        ]);

        Warengruppe::create([
            'wgnr' => $this->wgnr,
            'bezeichnung' => $this->bezeichnung,
        ]);

        session()->flash('message', 'Warengruppe erfolgreich hinzugefügt.');
        $this->resetInputFields();
    }

    public function edit($wgnr)
    {
        $warengruppe = Warengruppe::findOrFail($wgnr);
        $this->wgnr = $warengruppe->wgnr;
        $this->bezeichnung = $warengruppe->bezeichnung;
        $this->oldWgnr = $warengruppe->wgnr;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'wgnr' => 'required|string|max:20|unique:warengruppen,wgnr,' . $this->oldWgnr . ',wgnr',
            'bezeichnung' => 'required|string|max:80',
        ]);

        if ($this->oldWgnr) {
            $warengruppe = Warengruppe::find($this->oldWgnr);
            $warengruppe->update([
                'wgnr' => $this->wgnr,
                'bezeichnung' => $this->bezeichnung,
            ]);

            $this->updateMode = false;
            session()->flash('message', 'Warengruppe erfolgreich aktualisiert.');
            $this->resetInputFields();
        }
    }

    public function delete($wgnr)
    {
        if ($wgnr) {
            Warengruppe::where('wgnr', $wgnr)->delete();
            session()->flash('message', 'Warengruppe erfolgreich gelöscht.');
        }
    }


}
