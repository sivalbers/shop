<?php

namespace App\Services;

use App\Repositories\ArtikelRepository;
use App\Repositories\WarengruppeRepository;
use App\Repositories\ArtikelSortimentRepository;
use App\Repositories\WgHelperRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function PHPUnit\Framework\isEmpty;

class ArtikelService
{
    protected $artikelRepository;
    protected $warengruppeRepository;
    protected $artikelSortimentRepository;
    protected $wgHelperRepository;
    protected $logLevel;


//#REGION Logging

    private function shouldLog(string $level): bool
    {
        $allowedLogLevels = [
            'debug'   => 0,
            'info'    => 1,
            'warning' => 2,
            'error'   => 3,
        ];

        return $allowedLogLevels[$level] >= $allowedLogLevels[$this->logLevel];
    }

    private function logMessage(string $level, string $message, array $context = []): void
    {
        if ($this->shouldLog($level)) {
            Log::$level($message, $context);
        }
    }
//#REGIONEND

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
        $this->logLevel = 'debug';
    }

    public function createArtikelMitZuordnungen(array $data)
    {
        Log::info('createArtikelMitZuordnungen');
        // 1. Artikel speichern
        // 2. WG-Helper abrufen aufgrund der Category_ID
        // 3. Artikel_Sortiment anlegen/ändern

        $verlauf = [];

        $result = [ 'error' => false,
                    'errorMessage' => '' ];
        $artikel = null;

        try {
            DB::transaction(function () use ($data, $artikel, &$result) {
                // 1. Artikel speichern und zurückgeben

                $artikel = $this->artikelRepository->create($data);
                $this->logMessage('debug', 'Artikel gespeichert 1 von 3' );


                if (empty($artikel)) {
                    throw new \Exception("ArtikelService->Artikel konnte nicht erstellt werden.");
                }


                // 2. abrufen

                $wgHelper = $this->wgHelperRepository->getById($data['category_id'] ?? null);
                if ($wgHelper){

                    $this->logMessage('debug', 'Category gefunden 2 von 3' );
                }
                else{
                    $this->logMessage('debug', 'Category NICHT gefunden 2 von 3' );
                }

                if (!$wgHelper) {
                    throw new \Exception("WG-Helper nicht gefunden (ID: {$data['category_id']})");
                }

                // 3. Artikel_Sortiment anlegen/ändern
                if (!$this->artikelSortimentRepository->create([ 'item_number' => $artikel->artikelnr,
                                                                 'product_range' => $wgHelper->sortiment, ])) {

                    $this->logMessage('debug', 'Artikelsortiment fehler  3 von 3' );
                    throw new \Exception("Artikel_Sortiment Zuordnung nichtg gespeichert. (ID: {$data['category_id']})");
                }
                else {
                    $this->logMessage('debug', 'Artikelsortiment angelegt  3 von 3' );
                }

                $result[ 'artikel'] = $artikel ;

            });
        } catch (Throwable $e) {
            $result[] = [ 'error' => true,
                          'errorMessage' => 'Fehler: '.$e->getMessage()
                         ];
        }
        Log::info('Result');
        Log::info($result);

        return $result;
    }
}
