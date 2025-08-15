<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class PunchOut extends Controller
{
    public function handlePunchOutGet(Request $request): JsonResponse
    {

        // Hier verarbeitest du den PunchOut-Request und erstellst die Antwort.

        // Beispiel: Prüfe die übermittelten Daten
        $data = $request->all();

        Log::info('Get data:', [ $data ]);
        // Erstelle eine Beispielantwort
        /*
        $response = [
            'success' => true,
            'message' => 'PunchOut erfolgreich',
            'redirect_url' => 'http://shop.local/checkout',
        ];
        */


        return response()->json($data);
    }

    public function handlePunchOutPost(Request $request): JsonResponse
    {

        // Hier verarbeitest du den PunchOut-Request und erstellst die Antwort.

        // Beispiel: Prüfe die übermittelten Daten
        $data = $request->all();

        Log::info('Post data:', [ $data ]);
        // Erstelle eine Beispielantwort
        /*
        $response = [
            'success' => true,
            'message' => 'PunchOut erfolgreich',
            'redirect_url' => 'http://shop.local/checkout',
        ];
        */


        return response()->json($data);
    }



}
