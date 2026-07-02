<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        // Beperk de standaard VARCHAR-lengte tot 191 tekens.
        // Met utf8mb4 (4 bytes/teken) blijft een index dan onder de 1000-byte limiet
        // van MySQL/MariaDB. Zonder dit falen de migraties van framework-tabellen
        // (cache, sessions, password_reset_tokens) met "key too long", waardoor de
        // hele test-suite niet kan draaien.
        Schema::defaultStringLength(191);
    }
}
