<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Nachricht;
use App\Models\UserNachrichtenStatus;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
    public $kopfzeile = false;
    public $mail = false;
    public $kundennr;
    public $nachrichtId;
    public $mitlogin = false ;


    public $isModified = false ;
    public $showForm = false ;

    public function mount()
    {
        if (!auth::user()->isReporter()){
            return redirect('/startseite');
        }
        // Nachrichten laden, die in der Datenbank gespeichert sind
        $this->loadData();
    }

    public function loadData(){
        $this->nachrichten = Nachricht::orderByRaw('
        CASE
            WHEN bis IS NULL THEN 0
            WHEN bis >= ? THEN 0
            ELSE 1
        END, created_at DESC
    ', [now()])->get();
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
        $this->kopfzeile = false;
        $this->mail = false;
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
            'kopfzeile' => 'boolean',
            'mail' => 'boolean',
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
        $nachricht = Nachricht::updateOrCreate(['id' => $this->nachrichtId], $validatedData);

        $this->loadData();

        UserNachrichtenStatus::createNachricht($nachricht);

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
        // dd($nachricht->mitlogin);

        $this->nachrichtId = $nachricht->id;
        $this->kurztext = $nachricht->kurztext;
        $this->langtext = $nachricht->langtext;
        $this->von = optional($nachricht->von)->format('Y-m-d');
        $this->bis = optional($nachricht->bis)->format('Y-m-d');

        $this->links = $nachricht->links;
        $this->prioritaet = $nachricht->prioritaet;
        $this->kopfzeile = $nachricht->kopfzeile;
        $this->mail = $nachricht->mail;
        $this->mitlogin = $nachricht->mitlogin;


        $this->kundennr = $nachricht->kundennr;


    }

    public function delete($id)
    {

        // Nachricht löschen
        Nachricht::findOrFail($id)->delete();

        // Nachrichten neu laden
        $this->loadData();
    }

    public function render()
    {
        // Die Komponente rendern und die Nachrichten anzeigen
        return view('livewire.nachricht-component');
    }



}
