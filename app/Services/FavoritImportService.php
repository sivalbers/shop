<?php

namespace App\Services;

use App\Repositories\FavoritRepository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FavoritImportService
{
    public function __construct(){

    }


    public function importFavoritFile(callable $progressCallback)
    {
        $path = storage_path('app/favoriten_31_03_2025.csv');
        if (!file_exists($path)) {
            throw new \Exception("Datei nicht gefunden: $path");
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \Exception("Datei konnte nicht geöffnet werden.");
        }

        $header = fgetcsv($handle, 0, ';'); // Kopfzeile überspringen
        $zeile = 1;
        $gesamt = count(file($path)) - 1; // für Fortschrittsanzeige
        $i = 0;
        $e = 0;
        $fehlendeDebitoren = [];
        $fehlerhafte = [];
        $erfolg = [];

        $repo = new FavoritRepository();

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $zeile++;
            [$id, $artikelNr, $sortiment, $sort, $favoriten, $benutzerId, $debitorNr] = $row;

            $result = $repo->addFavorit($artikelNr, $favoriten, $sort, $debitorNr, $zeile);

            if ($result['status'] === 'ok') {
                $i++;
                $erfolg[] = $artikelNr;
            } elseif ($result['status'] === 'debitor_fehlend') {
                $fehlendeDebitoren[] = $debitorNr;
            } else {
                $e++;
                $fehlerhafte[] = $result;
            }

            if ($progressCallback) {
                $progressCallback(round($zeile / $gesamt * 100, 1));
            }
        }

        fclose($handle);

        return [
            'ok' => true,
            'fehlerfrei' => $i,
            'fehlerhaft' => $e,
            'erfolg' => $erfolg,
            'fehlerhafte' => $fehlerhafte,
            'debitoren_fehlen' => array_unique($fehlendeDebitoren),
        ];
    }



}

