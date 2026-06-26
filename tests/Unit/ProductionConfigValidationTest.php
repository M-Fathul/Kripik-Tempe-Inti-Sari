<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use RuntimeException;
use Tests\TestCase;

class ProductionConfigValidationTest extends TestCase
{
    public function test_boot_fails_when_flask_config_missing_in_production(): void
    {
        config(['services.flask.url' => '', 'services.flask.key' => '']);

        $this->app['env'] = 'production';

        $provider = new AppServiceProvider($this->app);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing required environment variables for production');

        $provider->boot();
    }
}
