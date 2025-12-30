<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\ServiceProviderSchedule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin User
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
            $this->command->info('✓ Super Admin user created');
        } else {
            $this->command->info('✓ Super Admin user already exists');
        }

        // 2. Plumber Service Provider
        $plumberCategory = Category::where('code', 'plumber')->first();
        if (!$plumberCategory) {
            $this->command->warn('⚠ Plumber category not found. Please run CategorySeeder first.');
            return;
        }

        $plumber = User::firstOrCreate(
            ['email' => 'plumber@test.com'],
            [
                'full_name' => 'Test Plumber',
                'company_name' => 'Test Plumbing Services',
                'email' => 'plumber@test.com',
                'password' => Hash::make('password123'),
                'whatsapp_number' => '32470123456',
                'phone' => '32470123456',
                'address' => 'Teststraat 123',
                'number' => '123',
                'postal_code' => '9000',
                'city' => 'Ghent',
                'country' => 'Belgium',
                'role' => 'plumber',
                'email_verified_at' => now(),
            ]
        );

        // Attach plumber to plumber category
        if (!$plumber->categories()->where('categories.id', $plumberCategory->id)->exists()) {
            $plumber->categories()->attach($plumberCategory->id);
        }

        // Create schedule for plumber
        ServiceProviderSchedule::firstOrCreate(
            ['user_id' => $plumber->id],
            [
                'timezone' => 'Europe/Brussels',
                'schedule_data' => ServiceProviderSchedule::getDefaultSchedule(),
                'holidays' => [],
                'vacations' => [],
                'last_updated' => now()
            ]
        );

        // Add default municipality coverage
        if (method_exists($plumber, 'addDefaultMunicipalityCoverage')) {
            $plumber->addDefaultMunicipalityCoverage();
        }

        if ($plumber->wasRecentlyCreated) {
            $this->command->info('✓ Plumber user created');
        } else {
            $this->command->info('✓ Plumber user already exists');
        }

        // 3. Gardener Service Provider
        $gardenerCategory = Category::where('code', 'gardener')->first();
        if (!$gardenerCategory) {
            $this->command->warn('⚠ Gardener category not found. Please run CategorySeeder first.');
            return;
        }

        $gardener = User::firstOrCreate(
            ['email' => 'gardener@test.com'],
            [
                'full_name' => 'Test Gardener',
                'company_name' => 'Test Gardening Services',
                'email' => 'gardener@test.com',
                'password' => Hash::make('password123'),
                'whatsapp_number' => '32470123457',
                'phone' => '32470123457',
                'address' => 'Teststraat 456',
                'number' => '456',
                'postal_code' => '1000',
                'city' => 'Brussels',
                'country' => 'Belgium',
                'role' => 'gardener',
                'email_verified_at' => now(),
            ]
        );

        // Attach gardener to gardener category
        if (!$gardener->categories()->where('categories.id', $gardenerCategory->id)->exists()) {
            $gardener->categories()->attach($gardenerCategory->id);
        }

        // Create schedule for gardener
        ServiceProviderSchedule::firstOrCreate(
            ['user_id' => $gardener->id],
            [
                'timezone' => 'Europe/Brussels',
                'schedule_data' => ServiceProviderSchedule::getDefaultSchedule(),
                'holidays' => [],
                'vacations' => [],
                'last_updated' => now()
            ]
        );

        // Add default municipality coverage
        if (method_exists($gardener, 'addDefaultMunicipalityCoverage')) {
            $gardener->addDefaultMunicipalityCoverage();
        }

        if ($gardener->wasRecentlyCreated) {
            $this->command->info('✓ Gardener user created');
        } else {
            $this->command->info('✓ Gardener user already exists');
        }

        // 4. Client User
        $client = User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'full_name' => 'Test Client',
                'email' => 'client@test.com',
                'password' => Hash::make('password123'),
                'whatsapp_number' => '32470111111',
                'phone' => '32470111111',
                'address' => 'Clientstraat 789',
                'number' => '789',
                'postal_code' => '9000',
                'city' => 'Ghent',
                'country' => 'Belgium',
                'role' => 'client',
                'email_verified_at' => now(),
            ]
        );

        if ($client->wasRecentlyCreated) {
            $this->command->info('✓ Client user created');
        } else {
            $this->command->info('✓ Client user already exists');
        }

        // Display summary
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  TEST USERS CREATED SUCCESSFULLY');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('Super Admin:');
        $this->command->info('  Email: admin@diensten.pro');
        $this->command->info('  Password: admin123');
        $this->command->info('  Login: /admin/login');
        $this->command->info('');
        $this->command->info('Plumber Service Provider:');
        $this->command->info('  Email: plumber@test.com');
        $this->command->info('  Password: password123');
        $this->command->info('  WhatsApp: +32 470 12 34 56');
        $this->command->info('  Location: Ghent (9000)');
        $this->command->info('');
        $this->command->info('Gardener Service Provider:');
        $this->command->info('  Email: gardener@test.com');
        $this->command->info('  Password: password123');
        $this->command->info('  WhatsApp: +32 470 12 34 57');
        $this->command->info('  Location: Brussels (1000)');
        $this->command->info('');
        $this->command->info('Client:');
        $this->command->info('  Email: client@test.com');
        $this->command->info('  Password: password123');
        $this->command->info('  WhatsApp: +32 470 11 11 11');
        $this->command->info('  Location: Ghent (9000)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}

