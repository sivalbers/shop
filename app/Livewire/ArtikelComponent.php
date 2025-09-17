<?php

namespace App\Livewire;

use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

use Livewire\Component;
use App\Models\Artikel;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

use App\Repositories\ImageRepository;


class ArtikelComponent extends Component
{
    use WithPagination;

    public $artFilter = '';
    public $bezFilter = '';
    public $statusFilter = '';
    public $wgFilter = '';
    private $pathOriginal = '';
    public $isModified = false;
    public $showForm = false ;


    public $isProcessing = false;   // zeigt an, ob gerade verarbeitet wird
    public $processedCount = 0;     // wie viele Dateien schon fertig sind
    public $totalCount = 0;         // wie viele insgesamt

    public array $loadedImages = [];
    public array $selectedToDelete = [];


    use WithFileUploads;

    public $images = []; // für mehrere Dateien

    public function __construct()
    {
        $this->pathOriginal = env('image_original', "public/products_original/");

    }

    public function render(){
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
      if (in_array($this->statusFilter, ['0', '1'])) {
          $query->where('gesperrt', '=', $this->statusFilter);
      }

      // Paginierung der Ergebnisse und Rückgabe an die View
      $artikels = $query->paginate(50);

      return view('livewire.artikelliste', compact('artikels'));

    }


    #[On('applyArtikelFilter')]
    public function applyArtikelFilters($art, $bez, $stat) {
        $this->artFilter = $art;
        $this->bezFilter = $bez;
        $this->statusFilter = $stat;

        //dd($art);
        // Initialisiere den Query Builder

    }


    public function saveImages()
    {


        // Validierung
        $this->validate([
            'images.*' => 'image|mimes:jpg,jpeg,png|max:5120', // max 2MB pro Bild
        ]);

        $savedPaths = [];

        foreach ($this->images as $image) {
            // Datei im Storage ablegen (z.B. storage/app/public/artikelbilder)
            $originalName = $image->getClientOriginalName(); // z. B. "mein-bild.png"

            $path = $image->storeAs($this->pathOriginal, $originalName, '');
            $savedPaths[] = $path;
        }
        $imageRep = new ImageRepository();

        foreach ($savedPaths as $path){
            Log::info(['Datei: ' => $path]);
            $imageRep->processItemImageFromFile($path);
        }

        // Falls du die Bildpfade in der DB speichern willst:
        // Beispiel: Bilder an einen Artikel hängen
        // ArtikelBild::create(['artikelnr' => $this->artikelnr, 'pfad' => $path]);

        // Nach dem Speichern zurücksetzen
        $this->reset('images');

        session()->flash('success', 'Bilder erfolgreich hochgeladen!');
        $this->showForm = false ;
    }

    public function showUploadForm(){
        $this->showForm = true ;
    }

    public function importFolder(){

        $imageRep = new ImageRepository();

        // $this->isProcessing = true;


        $files = Storage::files($this->pathOriginal);
        $this->totalCount = count($files);

        foreach ($files as $file) {
            try {
                $fileName = basename($file);
                $imageRep->processItemImageFromFile($fileName);


                // Update im Frontend auslösen
                // $this->dispatch('progressUpdated');
            } catch (\Exception $e) {
                Log::error("Fehler bei Datei {$file}: " . $e->getMessage());
            }
        }

        //$this->isProcessing = false;

    }

    public function loadImages($artikelnr)
    {
        $path = env('image_small', 'public/products_small/');
        $files = Storage::files($path);

        $artikelImages = [];

        foreach ($files as $file) {
            $filename = basename($file);
            if (str_starts_with($filename, $artikelnr)) {
                $artikelImages[] = $filename;
            }
        }

        $this->loadedImages[$artikelnr] = $artikelImages;

    }

    public function deleteSelectedImages($artikelnr)
    {
        $pathSmall = env('image_small', 'public/products_small/');
        $pathBig = env('image_big', 'public/products_big/');
        if (!isset($this->selectedToDelete[$artikelnr])) {
            return;
        }
        foreach ($this->selectedToDelete[$artikelnr] as $filename => $isChecked) {
            $filename = str_replace('__', '.', $filename);

            if ($isChecked) {
                Storage::delete($pathSmall . $filename);
                Storage::delete($pathBig . $filename);
            }
        }

        // Cleanup & reload
        unset($this->selectedToDelete[$artikelnr]);
        $this->loadImages($artikelnr);
    }


}
