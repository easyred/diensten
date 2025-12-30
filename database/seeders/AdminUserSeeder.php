<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@diensten.pro'],
            [
                'full_name' => 'Super Admin',
                'email' => 'admin@diensten.pro',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Super Admin user created successfully!');
        } else {
            $this->command->info('Super Admin user already exists.');
        }

        $this->command->info('Admin Login Credentials:');
        $this->command->info('Email: admin@diensten.pro');
        $this->command->info('Password: admin123');
        $this->command->info('Login URL: /admin/login');
    }
}
