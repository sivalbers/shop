<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Sortiment;

class SortimentComponent extends Component
{
    public $bezeichnung = '';
    public $updateMode = false;
    public $oldBezeichnung;
    public $edAnzeigename;
    public $edAbholung;
    public $sortimente;

    public $statusMessage;
    public $showEditWindow;


    public function mount(){
        $this->showEditWindow = false ;
    }


    public function render()
    {

        $this->loadItems();
        return view('livewire.sortiment-component');
    }

    private function loadItems(){
        $this->sortimente = Sortiment::all();
    }

    public function resetInputFields()
    {
        $this->bezeichnung = '';
        $this->edAnzeigename = '';
        $this->edAbholung = true ;
        $this->showEditWindow = false;
    }


    public function edit($bezeichnung)
    {
        $sortiment = Sortiment::findOrFail($bezeichnung);

        $this->bezeichnung = $sortiment->bezeichnung;
        $this->oldBezeichnung = $sortiment->bezeichnung;
        $this->edAnzeigename = $sortiment->anzeigename;
        $this->edAbholung = $sortiment->abholung;
        $this->updateMode = true;
        $this->showEditWindow = true;

    }

    public function create(){
        $this->updateMode = false;
        $this->showEditWindow = true;
    }

    public function store()
    {
        $this->validate([
            'bezeichnung' => 'required|string|max:20|unique:sortimente,bezeichnung',
        ]);

        Sortiment::create([
            'bezeichnung' => $this->bezeichnung,
            'anzeigename' => $this->edAnzeigename,
            'abholung' => $this->edAbholung,
        ]);

        // session()->flash('message', 'Sortiment erfolgreich hinzugefügt.');
        $this->statusMessage = 'Sortiment erfolgreich hinzugefügt.';
        $this->dispatch('status-updated');

        $this->resetInputFields();
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
                'anzeigename' => $this->edAnzeigename,
                'abholung' => $this->edAbholung,
            ]);

            $this->statusMessage = 'Sortiment erfolgreich aktualisiert.';
            $this->dispatch('status-updated');


            $this->resetInputFields();
            $this->updateMode = false;

        }

    }

    public function delete($bezeichnung)
    {
        if ($bezeichnung) {
            Sortiment::where('bezeichnung', $bezeichnung)->delete();

            $this->statusMessage = 'Sortiment erfolgreich gelöscht.';
            $this->dispatch('status-updated');

        }
    }

    public function cancel(){
        $this->resetInputFields();
        $this->updateMode = false;
        $this->statusMessage = 'Aktion abgebrochen.';
        $this->dispatch('status-updated');
    }
}
