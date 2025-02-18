<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Log::info('in Middleware angekommen');
        $apiLog = null;

        try{
            $apiLog = ApiLog::create([
            'version' => $request->header('x-version'),
            'httpmethod' => $request->method(),
            'pfad' => $request->path(),
            'key' => $request->header('x-key'),
            'session' => $request->header('x-session'),
            'token' => $request->header('x-token'),
            'data' => json_encode($request->all()),
            ]);

        } catch (\Exception $e) {
            Log::error([ 'Exception ist aufgetreten: ' => $e->getMessage()] );
        } finally {
        }

        // Anfrage weiterleiten
        $response = $next($request);

        // Response loggen
        $apiLog->update([
            'response' => json_encode($response->getContent()),
        ]);

        return $response;

    }
}
