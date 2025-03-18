<?php

namespace App\Repositories;

use App\Models\Warengruppe;
use App\Models\wghelper;

use Illuminate\Support\Facades\Log;

class WgHelperRepository
{
    public function getById($id){
        return WgHelper::find($id);
        //return WgHelper::findOrFail($id);
    }
}
