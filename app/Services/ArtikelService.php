<?php

namespace App\Services;

use App\Repositories\ArtikelRepository;
use App\Repositories\WarengruppeRepository;
use App\Repositories\ArtikelSortimentRepository;
use App\Repositories\WgHelperRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArtikelService
{
    protected $artikelRepository;
    protected $warengruppeRepository;
    protected $artikelSortimentRepository;
    protected $wgHelperRepository;

    public function __construct(
        ArtikelRepository $artikelRepository,
        WarengruppeRepository $warengruppeRepository,
        ArtikelSortimentRepository $artikelSortimentRepository,
        WgHelperRepository $wgHelperRepository
    ) {
        $this->artikelRepository = $artikelRepository;
        $this->warengruppeRepository = $warengruppeRepository;
        $this->artikelSortimentRepository = $artikelSortimentRepository;
        $this->wgHelperRepository = $wgHelperRepository;
    }

    public function createArtikelMitZuordnungen(array $data)
    {
        // 1. Artikel speichern
        // 2. WG-Helper abrufen aufgrund der Category_ID
        // 3. Artikel_Sortiment anlegen/ändern

        $result = [ 'error' => false,
                    'errorMessage' => '' ];

        try {
            DB::transaction(function () use ($data) {
                // 1. Artikel speichern und zurückgeben
                $artikel = $this->artikelRepository->create($data);

                if (!$artikel) {
                    throw new \Exception("Artikel konnte nicht erstellt werden.");
                }

                // 2. abrufen
                Log::info('Vor getByID()');
                $wgHelper = $this->wgHelperRepository->getById($data['category_id'] ?? null);

                if (!$wgHelper) {
                    throw new \Exception("WG-Helper nicht gefunden (ID: {$data['category_id']})");
                }

                // 3. Artikel_Sortiment anlegen/ändern
                if (!$this->artikelSortimentRepository->create([ 'item_number' => $artikel->artikelnr,
                                                                 'product_range' => $wgHelper->sortiment, ])) {
                    throw new \Exception("Artikel_Sortiment Zuordnung nichtg gespeichert. (ID: {$data['category_id']})");
                }
                Log::info('Nach der Anlage');

            });
        } catch (Throwable $e) {
            $result['error'] = true ;
            $result['errorMessage'] = 'Fehler: '.$e->getMessage() ;
            
        }
        return $result;
    }
}
