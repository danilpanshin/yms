<?php

namespace App\Providers;

use App\Drivers\FirebirdDriver;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        date_default_timezone_set('Europe/Moscow');
        URL::forceScheme('https');
    }
}
