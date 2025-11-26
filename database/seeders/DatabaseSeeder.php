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
            'name' => 'Mitra',
            'email' => 'mitra@example.com',
            'role' => 'mitra',
            'password' => bcrypt('password'),
        ]);

        // Akun Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@asimpenas.com',
            'role' => 'admin',
            'password' => bcrypt('adminada'),
        ]);

        // Akun Super Admin
        User::factory()->create([
            'name' => 'SuperAdmin',
            'email' => 'superadmin@asimpenas.com',
            'role' => 'super admin',
            'password' => bcrypt('password'),
        ]);

        $this->call(KaryawanSeeder::class);
    }
}
