<?php

namespace App\Livewire;

use App\Models\ApiLog;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ApiLogComponent extends Component
{
    public $logs; // Enthält alle Log-Einträge
    public $id, $version, $pfad, $key, $session, $token, $data, $response;
    public $showApiLogEdit = false;

    protected $rules = [
        'version' => 'nullable|string|max:255',
        'pfad' => 'nullable|string|max:255',
        'key' => 'nullable|string|max:255',
        'session' => 'nullable|string|max:255',
        'token' => 'nullable|string|max:255',
        'data' => 'nullable|string',
        'response' => 'nullable|string',
    ];

    public function mount()
    {
        $this->logs = ApiLog::orderby('created_at', 'desc')->get(); // Holt alle Logs aus der Datenbank
    }

    public function edit($id)
    {
        $log = ApiLog::findOrFail($id);
        $this->id = $log->id;
        $this->version = $log->version;
        $this->pfad = $log->pfad;
        $this->key = $log->key;
        $this->session = $log->session;
        $this->token = $log->token;
        $this->data = $log->data;
        $this->response = $log->response;

        $this->showApiLogEdit = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->id) {
            // Bearbeiten
            $log = ApiLog::findOrFail($this->id);
        } else {
            // Neuer Eintrag
            $log = new ApiLog();
        }

        $log->version = $this->version;
        $log->pfad = $this->pfad;
        $log->key = $this->key;
        $log->session = $this->session;
        $log->token = $this->token;
        $log->data = $this->data;
        $log->response = $this->response;
        $log->save();

        $this->resetForm();
        $this->logs = ApiLog::orderby('created_at')->get(); // Aktualisiert die Tabelle
        $this->showApiLogEdit = false;
    }

    public function delete($id)
    {
        ApiLog::findOrFail($id)->delete();
        $this->logs = ApiLog::all(); // Tabelle aktualisieren
    }

    public function resetForm()
    {
        $this->id = null;
        $this->version = '';
        $this->pfad = '';
        $this->key = '';
        $this->session = '';
        $this->token = '';
        $this->data = '';
        $this->response = '';
    }

    public function render()
    {
        return view('livewire.api-log-component');
    }
}
