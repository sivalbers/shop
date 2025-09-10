<?php

namespace App\Repositories;

use App\Models\Favorit;
use App\Models\FavoritPos;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Exception;

class ImageRepository
{

    private $pathOriginal;
    private $pathBig;
    private $pathSmall;
    private $pathArchive;

    public function __construct(){
        $this->pathOriginal = env('image_original', "public/products_original/" );
        $this->pathBig = env('image_big', "public/products_big/" );
        $this->pathSmall = env('image_small', "public/products_small/" );
        $this->pathArchive = env('image_archive', "public/products_archive/" );
    }

    function storeItemImage($base64Image, $artikelnr){
        // Prüfe, ob ein Bild vorhanden ist
        if (!$base64Image) {
            return null;
        }

        // Extrahiere Dateityp (png oder jpeg) und eigentliche Bilddaten
        list($format, $base64Data) = explode(':', $base64Image);

        // Definiere den MIME-Typ basierend auf dem Format
        $mimeType = ($format == 'png') ? 'image/png' : 'image/jpeg';
        $extension = ($format == 'png') ? 'png' : 'jpg';

        // Base64-Daten dekodieren
        $imageData = base64_decode($base64Data);

        // Generiere einen einzigartigen Dateinamen
        $fileName = $artikelnr . '.' . $extension;

        // Speicherort in Laravel (z.B. storage/app/public/items/)
        $path = $this->pathOriginal . $fileName;

        // Speichere das Bild mit Laravel's Storage-Funktion
        Storage::put($path, $imageData);

        // Rückgabe des Speicherpfads für die Datenbank
        return $path;
    }



}
