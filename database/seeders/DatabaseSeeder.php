<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (app()->environment('production') && empty($password)) {
            throw new RuntimeException('ADMIN_PASSWORD must be set in production.');
        }

        $superAdmin = User::where('email', $email)->first();

        if (empty($superAdmin)) {
            User::create([
                'name' => 'Super Admin',
                'email' => $email,
                'password' => bcrypt($password ?? 'password'),
                'role' => 'admin',
            ]);
        }
    }
}
