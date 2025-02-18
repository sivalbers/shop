<?php

namespace App\Jobs;

use App\Models\Bestellung;
use App\Repositories\BestellungRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;

class SendBestellungToErp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bestellung;

    public function __construct(Bestellung $bestellung)
    {
        $this->bestellung = $bestellung;
    }

    public function handle(BestellungRepository $bestellungRepository)
    {
        try {
            $bestellungRepository->sendToERP($this->bestellung);
        } catch (Exception $e) {
            Log::error("Bestellung {$this->bestellung->id} konnte nicht gesendet werden: " . $e->getMessage());

            // Job wird erneut versucht
            throw $e;
        }
    }

    // Max. 3 Wiederholungen mit jeweils 10 Minuten Abstand
    public function backoff()
    {
        return [600, 1200, 1800];
    }
}
