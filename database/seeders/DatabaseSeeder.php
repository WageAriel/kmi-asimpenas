<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Akun Mitra
        User::factory()->create([
            'name' => 'Mitra User',
            'email' => 'mitra@example.com',
            'role' => 'mitra',
            'password' => bcrypt('password'),
        ]);

        // Akun Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Akun Super Admin
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => 'super admin',
            'password' => bcrypt('password'),
        ]);

        $this->call(KaryawanSeeder::class);
    }
}
