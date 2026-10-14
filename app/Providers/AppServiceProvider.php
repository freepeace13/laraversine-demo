<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        // SQLite has no trig functions; Laraversine needs these four registered.
        $connection = DB::connection();

        if ($connection->getDriverName() === 'sqlite') {
            foreach (['radians' => 'deg2rad', 'cos' => 'cos', 'sin' => 'sin', 'acos' => 'acos'] as $name => $fn) {
                $connection->getPdo()->sqliteCreateFunction($name, $fn, 1);
            }
        }
    }
}
