<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Sortiment;

class SortimentComponent extends Component
{
    public $bezeichnung = '';
    public $updateMode = false;
    public $oldBezeichnung;
    public $sortimente;
    public $statusMessage;

    public function render()
    {
        $this->statusMessage = '';
        $this->loadItems();
        return view('livewire.sortiment-component');
    }




    private function loadItems(){
        $this->sortimente = Sortiment::all();
    }

    public function resetInputFields()
    {
        $this->bezeichnung = '';
    }

    public function store()
    {
        $this->validate([
            'bezeichnung' => 'required|string|max:20|unique:sortimente,bezeichnung',
        ]);

        Sortiment::create([
            'bezeichnung' => $this->bezeichnung,
        ]);

        // session()->flash('message', 'Sortiment erfolgreich hinzugefügt.');
        $this->statusMessage = 'Sortiment erfolgreich hinzugefügt.';
        $this->resetInputFields();
    }

    public function edit($bezeichnung)
    {
        $sortiment = Sortiment::findOrFail($bezeichnung);

        $this->bezeichnung = $sortiment->bezeichnung;
        $this->oldBezeichnung = $sortiment->bezeichnung;
        $this->updateMode = true;

    }

    public function update()
    {

        $this->validate([
            'bezeichnung' => 'required|string|max:20|unique:sortimente,bezeichnung,' . $this->oldBezeichnung . ',bezeichnung',
        ]);

        if ($this->oldBezeichnung) {
            $sortiment = Sortiment::find($this->oldBezeichnung);
            $sortiment->update([
                'bezeichnung' => $this->bezeichnung,
            ]);




            // session()->flash('message', 'Sortiment erfolgreich aktualisiert.');
            $this->statusMessage = 'Sortiment erfolgreich aktualisiert.';

            $this->resetInputFields();
            $this->updateMode = false;

        }

    }

    public function delete($bezeichnung)
    {
        if ($bezeichnung) {
            Sortiment::where('bezeichnung', $bezeichnung)->delete();
            //session()->flash('message', 'Sortiment erfolgreich gelöscht.');
            $this->statusMessage = 'Sortiment erfolgreich gelöscht.';
        }
    }

    public function cancel(){
        $this->resetInputFields();
        $this->updateMode = false;
        $this->statusMessage = 'Aktion abgebrochen.';
    }
}
