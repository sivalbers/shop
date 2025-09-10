<?php

namespace App\Services;

use App\Models\Bestellung;
use App\Models\BestellungPos;
use App\Models\Artikel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PunchoutService
{
    /**
     * Bestellung an die in Session('target') hinterlegte URL senden.
     *
     * @param  int|Bestellung  $bestellung   Bestell-Nr oder bereits geladenes Modell
     * @return array{ok:bool,status:int,body:mixed,payload:array,url:string}
     */
    public function sendBestellung($bestellung): array
    {
        // Bestellung laden (falls nur Nr übergeben)
        if (! $bestellung instanceof Bestellung) {
            $bestellung = Bestellung::query()->whereKey($bestellung)->firstOrFail();
        }

        // Positionen holen (sortiert) und zugehörige Artikel in einem Schwung laden
        $positionen = BestellungPos::query()
            ->where('bestellnr', $bestellung->nr)
            ->orderBy('sort')
            ->get();

        $artikelMap = Artikel::query()
            ->whereIn('artikelnr', $positionen->pluck('artikelnr'))
            ->get()
            ->keyBy('artikelnr');

        // Payload aufbauen
        $payload = $this->buildPayload($positionen, $artikelMap);

        $url = (string) Session::get('target'); // z. B. https://sap.example.com/oci
        if (empty($url)) {
            throw new \RuntimeException('Punchout target URL fehlt in Session("target").');
        }

        // POST (JSON) senden – mit Timeouts und einfacher Fehlerbehandlung
        $response = Http::timeout(15)->asJson()->post($url, $payload);

        // Logging (optional)
        Log::info('Punchout-POST', [
            'url'     => $url,
            'status'  => $response->status(),
            'payload' => $payload,
            'body'    => $response->json() ?? $response->body(),
        ]);

        return [
            'ok'     => $response->successful(),
            'status' => $response->status(),
            'body'   => $response->json() ?? $response->body(),
            'payload'=> $payload,   // hilfreich fürs Debugging
            'url'    => $url,
        ];
    }

    /**
     * Baut die JSON-Struktur gemäß Mapping.
     *
     * @param  \Illuminate\Support\Collection  $positionen   Sammlung von BestellungPos
     * @param  \Illuminate\Support\Collection  $artikelMap   keyBy('artikelnr')
     * @return array
     */
    private function buildPayload($positionen, $artikelMap): array
    {
        // OCI-Grundstrukturen (Index beginnt bei 1 – wie im Beispiel)
        $desc = $qty = $unit = $price = $curr = $priceUnit = $vendor = $vendormat = $extProdId = $attach = $cust3 = $cust5 = $manufacturer = $zzmat = [];
        $charset = $ociInterface = [];

        $i = 1;
        $longtextBlocks = []; // dynamische Keys "NEW_ITEM-LONGTEXT_{i}:132" => [ ... ]

        foreach ($positionen as $pos) {
            $art = $artikelMap->get($pos->artikelnr);

            // Fallbacks, falls Artikeldaten fehlen
            $bezeichnung = $art->bezeichnung ?? '';
            $langtext    = $art->langtext ?? null;

            // Preis: Wenn Positionspreis vorhanden, den nehmen; sonst Artikelpreis
            $einzelpreis = $pos->epreis ?? $art->vkpreis ?? 0;

            // Pflichtfelder (gemäß Mapping)
            $desc[(string)$i]       = $bezeichnung;
            $qty[(string)$i]        = (string) ($pos->menge ?? 0);
            $unit[(string)$i]       = 'C62';
            $price[(string)$i]      = number_format((float) $einzelpreis, 2, '.', '');
            $curr[(string)$i]       = 'EUR';
            $priceUnit[(string)$i]  = '1';
            $vendor[(string)$i]     = '350923';
            $vendormat[(string)$i]  = (string) $pos->artikelnr;
            $extProdId[(string)$i]  = (string) $pos->artikelnr;
            $attach[(string)$i]     = ''; // laut Vorgabe fix leer
            $cust3[(string)$i]      = '0.19';
            $cust5[(string)$i]      = 'LAGERMATERIAL';
            $manufacturer[(string)$i] = 'SIEVERDING';
            $zzmat[(string)$i]      = $art->wgnr ?? '';

            // Charset / OCI Interface pro Item (wie im Beispiel)
            $charset[(string)$i]    = 'utf8';
            $ociInterface[(string)$i] = null;

            // Longtext als eigener, dynamischer Block je Position – exakt wie im Beispiel
            if (!empty($langtext)) {
                $longtextBlocks["NEW_ITEM-LONGTEXT_{$i}:132"] = [ $langtext ];
            }

            $i++;
        }

        // Basis-Payload zusammenbauen
        $payload = [
            'http_content_charset'  => $charset,
            'oci_interface'         => $ociInterface,
            'NEW_ITEM-DESCRIPTION'  => $desc,
            'NEW_ITEM-QUANTITY'     => $qty,
            'NEW_ITEM-UNIT'         => $unit,
            'NEW_ITEM-PRICE'        => $price,
            'NEW_ITEM-CURRENCY'     => $curr,
            'NEW_ITEM-PRICEUNIT'    => $priceUnit,
            'NEW_ITEM-VENDOR'       => $vendor,
            'NEW_ITEM-VENDORMAT'    => $vendormat,
            'NEW_ITEM-EXT_PRODUCT_ID'=> $extProdId,
            'NEW_ITEM-ATTACHMENT_TITLE' => $attach,
            'NEW_ITEM-CUST_FIELD3'  => $cust3,
            'NEW_ITEM-CUST_FIELD5'  => $cust5,
            'NEW_ITEM-MANUFACTURER' => $manufacturer,
            'NEW_ITEM-ZZMATGROUP'   => $zzmat,
        ];

        // Dynamische Longtext-Blöcke hinzufügen
        foreach ($longtextBlocks as $key => $value) {
            $payload[$key] = $value;
        }

        return $payload;
    }
}
