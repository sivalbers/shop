<?php

namespace App\Repositories;

use App\Models\Favorit;
use App\Models\FavoritPos;
use App\Models\Debitor;

use Illuminate\Support\Facades\Log;
use Exception;

class FavoritRepository
{
    public function addFavorit($artikelNr, $sortimentName, $sort, $debitorNr, $zeile)
    {
        if ($sort > 65534){
            $sort = 65534;
        }
        try {
            $debitorExists = Debitor::where('nr', $debitorNr)->exists();
            if (!$debitorExists) {
                return [
                    'status' => 'debitor_fehlend',
                    'artikelNr' => $artikelNr,
                    'zeile' => $zeile,
                ];
            }

            $favHeader = Favorit::firstOrCreate([
                'kundennr' => $debitorNr,
                'user_id' => 0,
                'name' => $sortimentName,
            ]);

            if (!$favHeader->id) {
                return [
                    'status' => 'fehler',
                    'artikelNr' => $artikelNr,
                    'zeile' => $zeile,
                    'fehler' => 'Favorit konnte nicht erstellt werden',
                ];
            }

            $exists = FavoritPos::where('favoriten_id', $favHeader->id)
                                ->where('artikelnr', $artikelNr)
                                ->exists();

            if (!$exists) {
                FavoritPos::create([
                    'favoriten_id' => $favHeader->id,
                    'artikelnr' => $artikelNr,
                    'sort' => $sort,
                ]);
            }

            return [
                'status' => 'ok',
                'artikelNr' => $artikelNr,
                'zeile' => $zeile,
            ];

        } catch (\Exception $e) {
            Log::error(sprintf('Fehler in Zeile %d: %s', $zeile, $e->getMessage()));
            return [
                'status' => 'fehler',
                'artikelNr' => $artikelNr,
                'zeile' => $zeile,
                'fehler' => $e->getMessage(),
            ];
        }
    }

}
