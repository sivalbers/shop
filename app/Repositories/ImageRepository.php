<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageRepository
{
    private $pathOriginal;
    private $pathBig;
    private $pathSmall;
    private $pathArchive;

    public function __construct()
    {
        $this->pathOriginal = env('image_original', "public/products_original/");
        $this->pathBig = env('image_big', "public/products_big/");
        $this->pathSmall = env('image_small', "public/products_small/");
        $this->pathArchive = env('image_archive', "public/products_archive/");
    }

    public function storeItemImage($base64Image, $artikelnr)
    {
        if (!$base64Image) {
            return null;
        }

        // Dateiname bereinigen
        $artikelnrClean = explode(' ', $artikelnr)[0];

        // Format und Bilddaten trennen
        list($format, $base64Data) = explode(':', $base64Image);
        $mimeType = ($format == 'png') ? 'image/png' : 'image/jpeg';
        $extension = ($format == 'png') ? 'png' : 'jpg';

        $imageData = base64_decode($base64Data);
        $fileName = $artikelnrClean . '.' . $extension;
        $originalPath = $this->pathOriginal . $fileName;

        // Original speichern
        Storage::put($originalPath, $imageData);

        // Mit Intervention Image weiterverarbeiten
        $image = Image::make($imageData);

        // BIG: z.B. 1024x768 max (für Webdarstellung)
        $imageBig = clone $image;
        $imageBig->resize(1024, 768, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        Storage::put($this->pathBig . $fileName, (string) $imageBig->encode($extension));

        // SMALL: z.B. 150x150 (für Vorschau wie im Screenshot)
        $imageSmall = clone $image;
        $imageSmall->fit(150, 150);
        Storage::put($this->pathSmall . $fileName, (string) $imageSmall->encode($extension));

        // ARCHIVE: Original verschieben
        Storage::move($originalPath, $this->pathArchive . $fileName);

        return $this->pathBig . $fileName;
    }

    public function processItemImageFromFilexx($fileName)
    {
        if (!$fileName) {
            return null;
        }

        $fileName = basename($fileName);

        // Dateiname bereinigen
        $artikelnrClean = explode(' ', $fileName)[0];

        // Dateiendung ermitteln (jpg/png)
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            throw new \Exception("Ungültiges Bildformat: $extension");
        }

        $originalPath = $this->pathOriginal . $fileName;

        if (!Storage::exists($originalPath)) {
            throw new \Exception("Datei nicht gefunden: $originalPath");
        }

        // Original laden
        $imageData = Storage::get($originalPath);
        $image = \Intervention\Image\Facades\Image::make($imageData);

        // Neuer Dateiname (immer saubere Artikelnr + Endung)
        $cleanFileName = $artikelnrClean . '.' . $extension;

        // BIG: 1024x768 max
        $imageBig = clone $image;
        $imageBig->resize(1024, 768, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        Storage::put($this->pathBig . $cleanFileName, (string) $imageBig->encode($extension));

        // SMALL: 150x150 (Vorschau)
        $imageSmall = clone $image;
        $imageSmall->fit(150, 150);
        Storage::put($this->pathSmall . $cleanFileName, (string) $imageSmall->encode($extension));

        // ARCHIVE: Original verschieben
        Storage::move($originalPath, $this->pathArchive . $cleanFileName);

        // Pfad zum BIG-Bild zurückgeben
        return $this->pathBig . $cleanFileName;
    }

    public function processItemImageFromFile($fileName)
    {
        if (!$fileName) {
            return null;
        }

        $fileName = basename($fileName);

        // Dateiname ohne Extension
        $filenameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

        // Optional: Leerzeichen und Sonderzeichen kürzen
        $artikelnrClean = explode(' ', $filenameWithoutExt)[0];

        // Dateiendung ermitteln
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            throw new \Exception("Ungültiges Bildformat: $extension");
        }

        $originalPath = $this->pathOriginal . $fileName;

        if (!Storage::exists($originalPath)) {
            throw new \Exception("Datei nicht gefunden: $originalPath");
        }

        // Original laden
        $imageData = Storage::get($originalPath);
        $image = \Intervention\Image\Facades\Image::make($imageData);

        // Sauberer Dateiname
        $cleanFileName = $artikelnrClean . '.' . $extension;

        // BIG: 1024x768 max
        $imageBig = clone $image;
        $imageBig->resize(1024, 768, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        Storage::put($this->pathBig . $cleanFileName, (string) $imageBig->encode($extension));

        // SMALL: 150x150 Vorschau
        $imageSmall = clone $image;
        $imageSmall->fit(150, 150);
        Storage::put($this->pathSmall . $cleanFileName, (string) $imageSmall->encode($extension));

        // ARCHIVE: Original verschieben
        Storage::move($originalPath, $this->pathArchive . $cleanFileName);

        return $this->pathBig . $cleanFileName;
    }


    public function processAllImagesFromOriginal()
    {
        // alle Dateien im Original-Ordner holen
        $files = Storage::files($this->pathOriginal);

        if (empty($files)) {
            \Log::info("Keine Dateien gefunden in: {$this->pathOriginal}");
            return;
        }

        foreach ($files as $file) {
            try {
                // Nur den Dateinamen extrahieren
                $fileName = basename($file);

                // bestehende Funktion aufrufen
                $result = $this->processItemImageFromFile($fileName);

                \Log::info("Bild verarbeitet: {$fileName} → {$result}");
            } catch (\Exception $e) {
                \Log::error("Fehler bei Datei {$file}: " . $e->getMessage());
            }
        }
    }


}
