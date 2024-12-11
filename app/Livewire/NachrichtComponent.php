<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Nachricht;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NachrichtComponent extends Component
{

    public $nachrichten;
    public $kurztext;
    public $langtext;
    public $von;
    public $bis;
    public $links;
    public $prioritaet = 'normal'; // Vorgabewert
    public $startseite = false;
    public $kundennr;
    public $nachrichtId;
    public $mitlogin = false ;

    public $isModified = false ;
    public $showForm = false ;

    public function mount()
    {
        // Nachrichten laden, die in der Datenbank gespeichert sind

        //$this->nachrichten = Nachricht::all();
        $this->nachrichten = Nachricht::orderby('von', 'asc')->get();
        foreach ($this->nachrichten as $nachricht){
            Log::info('mount() mitLogin',[ $nachricht->id, $nachricht->mitlogin ]);
        }
    }

    public function resetInputFields()
    {
        // Eingabefelder leeren

        $this->kurztext = '';
        $this->langtext = '';
        $this->von = '';
        $this->bis = '';
        $this->links = '';
        $this->prioritaet = 'normal';
        $this->startseite = false;
        $this->kundennr = '';
        $this->nachrichtId = null;
        $this->mitlogin = false ;
    }

    public function store()
    {

        // Validierung der Eingabefelder
        $validatedData = $this->validate([
            'kurztext' => 'required|max:100',
            'langtext' => 'nullable',
            'von' => 'nullable|date',
            'bis' => 'nullable|date',
            'links' => 'nullable',
            'prioritaet' => 'required|in:normal,mittel,hoch',
            'startseite' => 'boolean',
            'mitlogin' => 'boolean',
            'kundennr' => 'nullable|integer',
        ]);

        $this->checkNull($validatedData['langtext']);
        $this->checkNull($validatedData['von']);
        $this->checkNull($validatedData['bis']);
        $this->checkNull($validatedData['links']);
        $this->checkNull($validatedData['kundennr']);


        //dd($validatedData);
        // Erstellen oder Aktualisieren der Nachricht
        Nachricht::updateOrCreate(['id' => $this->nachrichtId], $validatedData);

        // Nachrichten neu laden orderby('prioritaet', 'desc')->
        $this->nachrichten = Nachricht::orderby('von', 'desc')->get();

        // Felder zurücksetzen
        $this->resetInputFields();
        $this->showForm = false;
    }

    function checkNull(&$value) {
        if ($value === ''){
            $value = null;
        }
    }

    public function newMessage(){
        $this->resetInputFields();
        $this->showForm = true ;
    }

    public function edit($id)
    {
        $this->showForm = true ;
        // Nachricht finden und Felder füllen
        $nachricht = Nachricht::findOrFail($id);
        $this->nachrichtId = $nachricht->id;
        $this->kurztext = $nachricht->kurztext;
        $this->langtext = $nachricht->langtext;
        $this->von = optional($nachricht->von)->format('Y-m-d');
        $this->bis = optional($nachricht->bis)->format('Y-m-d');

        $this->links = $nachricht->links;
        $this->prioritaet = $nachricht->prioritaet;
        $this->startseite = $nachricht->startseite === 1 ? true : false ;
        $this->mitlogin = $nachricht->mitlogin === 1 ? true : false ;

        $this->kundennr = $nachricht->kundennr;


    }

    public function delete($id)
    {

        // Nachricht löschen
        Nachricht::findOrFail($id)->delete();

        // Nachrichten neu laden
        $this->nachrichten = Nachricht::all();
    }

    public function render()
    {
        // Die Komponente rendern und die Nachrichten anzeigen
        return view('livewire.nachricht-component');
    }
}
