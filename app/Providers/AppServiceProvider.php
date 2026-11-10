<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
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
        // `artisan serve` membuang env var di luar daftar passthrough. Tanpa TEMP/TMP,
        // PHP di Windows tidak bisa menulis file temporary untuk body POST besar.
        ServeCommand::$passthroughVariables = [
            ...ServeCommand::$passthroughVariables,
            'TEMP',
            'TMP',
        ];
    }
}
