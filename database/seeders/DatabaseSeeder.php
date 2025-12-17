<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bean;
use App\Models\Grinder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Barista',
            'email' => 'admin@espresso.com',
            'password' => bcrypt('password123'),
        ]);

        // Create sample beans
        Bean::create([
            'name' => 'Ethiopia Guji',
            'origin' => 'Ethiopia',
            'roastery' => 'XYZ Coffee',
            'roast_level' => 'medium',
            'roast_date' => now()->subDays(5),
            'notes' => 'Floral, tea-like',
        ]);

        Bean::create([
            'name' => 'Colombia Huila',
            'origin' => 'Colombia',
            'roastery' => 'ABC Roasters',
            'roast_level' => 'light',
            'roast_date' => now()->subDays(8),
            'notes' => 'Citrus, berry notes',
        ]);

        // Create sample grinders
        Grinder::create([
            'name' => 'EK43',
            'model' => 'Mahlkönig EK43',
            'notes' => 'Main bar grinder',
        ]);

        Grinder::create([
            'name' => 'Mythos One',
            'model' => 'Nuova Simonelli Mythos One',
            'notes' => 'Training bar',
        ]);
    }
}
