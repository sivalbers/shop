<?php

use Illuminate\Support\Facades\Storage;


if (!function_exists('imageExists')) {
    function imageExists($artikelnr)
    {
        if (Storage::exists("public/products/$artikelnr.jpg")) {
            return "$artikelnr.jpg";
        } elseif (Storage::exists("public/products/$artikelnr.png")) {
            return "$artikelnr.png";
        }

        return '';
    }

}

if (!function_exists('imageExistsAll')) {
    function imageExistsAll($artikelnr)
    {
        static $allFiles = null; // Caching für alle Dateien im Verzeichnis

        $directory = "public/products/";

        // Lade alle Dateien nur einmal in die statische Variable
        if ($allFiles === null) {
            $allFiles = Storage::files($directory);
        }

        $mainImage = null;
        $otherImages = [];

        foreach ($allFiles as $file) {
            $filename = basename($file);

            // Prüfen, ob es das Hauptbild ohne Unterstrich ist
            if ($filename === "{$artikelnr}.jpg" || $filename === "{$artikelnr}.png") {
                $mainImage = $filename;
            }
            // Prüfen, ob es ein Bild mit Unterstrich (_1, _2, ...) ist
            elseif (preg_match("/^{$artikelnr}_[0-9]+\.(jpg|jpeg|png|gif)$/i", $filename)) {
                $otherImages[] = $filename;
            }
        }

        // Natürliche Sortierung für Bilder mit _1, _2, _3, ...
        natsort($otherImages);

        return array_merge($mainImage ? [$mainImage] : [], $otherImages);
    }
}


