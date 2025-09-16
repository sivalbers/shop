<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StammdatenManagerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !$user->isStammdatenManager()) {
            abort(403, 'Zugriff verweigert.');
        }

        return $next($request);
    }
}
