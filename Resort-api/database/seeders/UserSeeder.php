<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
            'phone' => '09171234567',
            'password' => 'password123',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'user@email.com',
            'phone' => '09181234567',
            'password' => 'password123',
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
