<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = User::where("email","admin@gmail.com")->first();

        if (empty($superAdmin)) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'ceadmin@gmail.com',
                'password' => bcrypt('admin'),
                'role' => 'admin',
            ]);
        }
        
    }
}
