<?php

namespace App\Repositories;

use App\Models\Warengruppe;
use Illuminate\Support\Facades\Log;

class WarengruppeRepository
{

    public function getByCode($code)
    {
        Log::info('getByCode()');
        try{
        return Warengruppe::findOrFail($code);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } finally {
            Log::info('Fehlerfrei');
        }
    }

    public function getAll()
    {
        return Warengruppe::all();
    }

    public function create(array $data)
    {
        return Warengruppe::create($data);
    }

    public function update($id, array $data)
    {
        $warengruppe = Warengruppe::findOrFail($id);
        $warengruppe->update($data);
        return $warengruppe;
    }

    public function delete($id)
    {
        $warengruppe = Warengruppe::findOrFail($id);
        $warengruppe->delete();
    }
}
