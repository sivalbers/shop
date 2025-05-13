<?php

namespace App\Services;

use App\Models\FavoritPos;

class FavoritenPosExporter
{
    public function exportSingle($favoritenId, $handle)
    {

        fwrite($handle, "\xEF\xBB\xBF");
        
        fputcsv($handle, ['artikelnr', 'bezeichnung', 'sort'], ';'); // Header

        FavoritPos::where('favoriten_id', $favoritenId)->each(function ($row) use ($handle) {
            fputcsv($handle, [
                $row->artikelnr,
                $row->artikel->bezeichnung,
                $row->sort,
            ], ';');
        });
    }

}
