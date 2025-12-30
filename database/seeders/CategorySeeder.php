<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(['code' => 'plumber'], [
            'name' => 'Plumber Service',
            'logo_url' => '/images/plumber_logo.png',
            'domain' => 'plumber.test',
            'is_active' => true,
        ]);

        Category::firstOrCreate(['code' => 'gardener'], [
            'name' => 'Gardening Service',
            'logo_url' => '/images/gardener_logo.png',
            'domain' => 'gardener.test',
            'is_active' => true,
        ]);
    }
}
