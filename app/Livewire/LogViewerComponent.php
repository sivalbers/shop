<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class LogViewerComponent extends Component
{
    public $logLines = [];
    protected string $logFile;

    public function mount()
    {
        // Standardmäßig befindet sich die Log-Datei in storage/logs/laravel.log
        $this->logFile = storage_path('logs/laravel.log');
        $this->loadLogs();
    }

    /**
     * Lädt den Inhalt der Log-Datei.
     */
    public function loadLogs(): void
    {
        $logFile = $this->getLogFilePath();

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            // Optional: Umkehren, sodass die neuesten Zeilen oben stehen
            $this->logLines = array_reverse(explode(PHP_EOL, $content));
        } else {
            $this->logLines = [];
        }
    }


    protected function getLogFilePath(): string
    {
        return storage_path('logs/laravel.log');
    }

    /**
     * Leert die Log-Datei und lädt den neuen (leeren) Inhalt.
     */
    public function clearLogs(): void
    {
        $logFile = $this->getLogFilePath();

        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        session()->flash('success', 'Log-Datei wurde geleert.');
        $this->loadLogs();
    }

    public function render()
    {
        return view('livewire.log-viewer');
    }
}
