<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware; // 1. Adım: AdminMiddleware sınıfını import et

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php', // Bu satırı ekledik, doğru.
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 2. Adım: Middleware'e bir takma ad (alias) ver
        // Artık Laravel 'admin' kelimesini gördüğünde hangi dosyayı çalıştıracağını bilecek.
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();