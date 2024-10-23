<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '/punchout',
            //'http://shop.local/sortimente',
            //'http://example.com/foo/*',
        ]);
      //  $middleware->append(AdminMiddleware::class); => Middleware wird immer aufgerufen
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
