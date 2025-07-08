<?php

namespace App\Repositories;

use App\Models\Warengruppe;
use App\Models\WgHelper;

use Illuminate\Support\Facades\Log;

class WgHelperRepository
{
    public function getById($id){
        return WgHelper::find($id);
        //return WgHelper::findOrFail($id);
    }


    private function findByWgNr($wgnr){
        return WgHelper::where('wgnr', $wgnr)->first();
    }

    private function findByName($wgName){
        return WgHelper::where('name', $wgName)->first();
    }

    public function create($wgHelper)
    {

        // Log::info(sprintf('wgHelper->wg:%s ->sortiment', $wgHelper->warengruppe, $wgHelper->sortiment )) ;
        Log::info('wgHelper: ' . json_encode($wgHelper));

        // Log::info('wgHelper:', print_r($wgHelper, true));

        try {
            $wg = $this->updateRecFromData($wg, $wgHelper);
        } catch (\Throwable $e) {
            // Fehler beim Speichern behandeln
            $this->logMessage('error', 'Create:: Fehler beim konvertieren der Warengruppe: ' . $e->getMessage(), []);
            return false;
        }

        $wgh = $this->findByName($name);

        try {
            // Validierung des Datensatzes
            if (!$this->validateRec($wg)) {
                return false;
            }

            if ($wg->save()) {
                return $wg;
            }

            // Optional: Loggen, falls Speichern nicht erfolgreich war, ohne Exception
            $this->logMessage('warning', 'Warengruppe konnte nicht gespeichert werden.', []);
            return false;

        } catch (\Exception $e) {
            $this->logMessage('error', 'Warengruppe: Fehler beim Speichern des Artikels: ' . $e->getMessage(), []);
            return false;
        }
    }


    public function delete($id)
    {
        $wgh = WgHelper::findOrFail($id);
        $wgh->delete();
        //$warengruppe = Warengruppe::findOrFail($id);
        //$warengruppe->delete();
    }

}
