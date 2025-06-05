<?php

use App\Http\Middleware\CheckIsAdmin;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\CheckIsDriver;
use App\Http\Middleware\CheckIsManager;
use App\Http\Middleware\CheckIsStockAdmin;
use App\Http\Middleware\CheckIsSupplier;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => CheckAuth::class,
            'is_stock_admin' => CheckIsStockAdmin::class,
            'is_supplier' => CheckIsSupplier::class,
            'is_manager' => CheckIsManager::class,
            'is_driver' => CheckIsDriver::class,
            'is_admin' => CheckIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
