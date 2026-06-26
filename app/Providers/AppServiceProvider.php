<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentTimezone;
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
        $this->validateProductionConfig();

        FilamentTimezone::set('Asia/Jakarta');
    }

    private function validateProductionConfig(): void
    {
        if (! $this->app->environment('production')) {
            return;
        }

        $missing = [];

        if (empty(config('services.flask.url'))) {
            $missing[] = 'FLASK_API_URL';
        }

        if (empty(config('services.flask.key'))) {
            $missing[] = 'FLASK_API_KEY';
        }

        if ($missing !== []) {
            throw new \RuntimeException(
                'Missing required environment variables for production: '.implode(', ', $missing)
            );
        }
    }
}
