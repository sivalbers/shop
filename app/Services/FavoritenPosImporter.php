<?php

namespace App\Services;

use App\Models\FavoritPos;
use Illuminate\Support\Facades\Log;

class FavoritenPosImporter
{
    public function importFromFile($favoritenId, bool $override, string $filePath):void
    {

        $file = fopen($filePath, 'r');
        // Header überspringen
        fgetcsv($file, 0, ';');

        if ($override){
            FavoritPos::where('favoriten_id', $favoritenId)->delete();
        }

        while (($data = fgetcsv($file, 1000, ';')) !== false) {

            if (count($data) >= 3) {
                FavoritPos::create([
                    'favoriten_id' => $favoritenId,
                    'artikelnr'    => $data[0],
                    'bezeichnung'  => $data[1],
                    'sort'         => $data[2],
                ]);
            }
        }
        fclose($file);

    }
}
