<?php

namespace Tests\Unit;

use Database\Seeders\DatabaseSeeder;
use RuntimeException;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    public function test_seeder_requires_admin_password_in_production(): void
    {
        $this->app['env'] = 'production';
        putenv('ADMIN_PASSWORD=');
        unset($_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_PASSWORD']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ADMIN_PASSWORD must be set in production.');

        (new DatabaseSeeder)->run();
    }
}
