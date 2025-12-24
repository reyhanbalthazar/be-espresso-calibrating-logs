<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bean;
use App\Models\Grinder;
use App\Models\CoffeeShop;
use App\Models\CalibrationSession;
use App\Models\Shot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create coffee shops
        $coffeeShop1 = CoffeeShop::create([
            'name' => 'Espresso Excellence',
            'address' => '123 Coffee Street, Seattle, WA',
            'phone' => '(555) 123-4567',
            'email' => 'info@espressoexcellence.com',
        ]);

        $coffeeShop2 = CoffeeShop::create([
            'name' => 'Brew Haven',
            'address' => '456 Bean Avenue, Portland, OR',
            'phone' => '(555) 987-6543',
            'email' => 'hello@brewhaven.com',
        ]);

        $coffeeShop3 = CoffeeShop::create([
            'name' => 'The Daily Grind',
            'address' => '789 Roast Road, San Francisco, CA',
            'phone' => '(555) 456-7890',
            'email' => 'contact@dailygrind.com',
        ]);

        // Create users
        $user1 = User::create([
            'name' => 'Admin Barista',
            'email' => 'admin@espresso.com',
            'password' => Hash::make('password123'),
            'coffee_shop_id' => $coffeeShop1->id,
        ]);

        $user2 = User::create([
            'name' => 'Senior Barista',
            'email' => 'senior@espresso.com',
            'password' => Hash::make('password123'),
            'coffee_shop_id' => $coffeeShop1->id,
        ]);

        $user3 = User::create([
            'name' => 'Head Roaster',
            'email' => 'headroaster@brewhaven.com',
            'password' => Hash::make('password123'),
            'coffee_shop_id' => $coffeeShop2->id,
        ]);

        $user4 = User::create([
            'name' => 'Shift Supervisor',
            'email' => 'supervisor@dailygrind.com',
            'password' => Hash::make('password123'),
            'coffee_shop_id' => $coffeeShop3->id,
        ]);

        // Create sample beans for each coffee shop
        $beans = [
            // Coffee Shop 1
            [
                'name' => 'Ethiopia Guji',
                'origin' => 'Ethiopia',
                'roastery' => 'XYZ Coffee',
                'roast_level' => 'medium',
                'roast_date' => now()->subDays(5),
                'notes' => 'Floral, tea-like with bright acidity',
                'coffee_shop_id' => $coffeeShop1->id,
            ],
            [
                'name' => 'Colombia Huila',
                'origin' => 'Colombia',
                'roastery' => 'ABC Roasters',
                'roast_level' => 'light',
                'roast_date' => now()->subDays(8),
                'notes' => 'Citrus, berry notes with caramel sweetness',
                'coffee_shop_id' => $coffeeShop1->id,
            ],
            [
                'name' => 'Kenya AA',
                'origin' => 'Kenya',
                'roastery' => 'Premium Coffee Co.',
                'roast_level' => 'medium',
                'roast_date' => now()->subDays(3),
                'notes' => 'Blackcurrant, wine-like with bright acidity',
                'coffee_shop_id' => $coffeeShop1->id,
            ],
            [
                'name' => 'Guatemala Antigua',
                'origin' => 'Guatemala',
                'roastery' => 'Artisan Roasters',
                'roast_level' => 'dark',
                'roast_date' => now()->subDays(7),
                'notes' => 'Chocolate, spice with full body',
                'coffee_shop_id' => $coffeeShop1->id,
            ],
            [
                'name' => 'Costa Rica Tarrazu',
                'origin' => 'Costa Rica',
                'roastery' => 'Mountain Roasters',
                'roast_level' => 'light',
                'roast_date' => now()->subDays(2),
                'notes' => 'Clean, bright with honey sweetness',
                'coffee_shop_id' => $coffeeShop1->id,
            ],

            // Coffee Shop 2
            [
                'name' => 'Brazil Santos',
                'origin' => 'Brazil',
                'roastery' => 'Tropical Roasters',
                'roast_level' => 'medium',
                'roast_date' => now()->subDays(4),
                'notes' => 'Nutty, chocolatey with low acidity',
                'coffee_shop_id' => $coffeeShop2->id,
            ],
            [
                'name' => 'Sumatra Mandheling',
                'origin' => 'Indonesia',
                'roastery' => 'Island Roasters',
                'roast_level' => 'dark',
                'roast_date' => now()->subDays(6),
                'notes' => 'Earthy, full body with herbal notes',
                'coffee_shop_id' => $coffeeShop2->id,
            ],
            [
                'name' => 'Jamaica Blue Mountain',
                'origin' => 'Jamaica',
                'roastery' => 'Caribbean Coffee',
                'roast_level' => 'medium',
                'roast_date' => now()->subDays(1),
                'notes' => 'Mild, well-balanced with bright acidity',
                'coffee_shop_id' => $coffeeShop2->id,
            ],

            // Coffee Shop 3
            [
                'name' => 'Panama Geisha',
                'origin' => 'Panama',
                'roastery' => 'Specialty Coffee Co.',
                'roast_level' => 'light',
                'roast_date' => now()->subDays(2),
                'notes' => 'Jasmine, bergamot with complex floral notes',
                'coffee_shop_id' => $coffeeShop3->id,
            ],
            [
                'name' => 'Yirgacheffe',
                'origin' => 'Ethiopia',
                'roastery' => 'Highland Roasters',
                'roast_level' => 'light',
                'roast_date' => now()->subDays(4),
                'notes' => 'Lemon, bergamot with tea-like body',
                'coffee_shop_id' => $coffeeShop3->id,
            ],
            [
                'name' => 'Honduras Marcala',
                'origin' => 'Honduras',
                'roastery' => 'Central American Roasters',
                'roast_level' => 'medium',
                'roast_date' => now()->subDays(3),
                'notes' => 'Caramel, milk chocolate with balanced acidity',
                'coffee_shop_id' => $coffeeShop3->id,
            ],
        ];

        foreach ($beans as $beanData) {
            Bean::create($beanData);
        }

        // Create sample grinders for each coffee shop
        $grinders = [
            // Coffee Shop 1
            [
                'name' => 'EK43',
                'model' => 'Mahlkönig EK43',
                'notes' => 'Main bar grinder with excellent consistency',
                'coffee_shop_id' => $coffeeShop1->id,
            ],
            [
                'name' => 'Mythos One',
                'model' => 'Nuova Simonelli Mythos One',
                'notes' => 'Training bar grinder',
                'coffee_shop_id' => $coffeeShop1->id,
            ],
            [
                'name' => 'Commander E',
                'model' => 'Mahlkönig Commander E',
                'notes' => 'Backup grinder for high volume',
                'coffee_shop_id' => $coffeeShop1->id,
            ],

            // Coffee Shop 2
            [
                'name' => 'K30 Vario',
                'model' => 'Baratza K30 Vario',
                'notes' => 'Drip coffee grinder',
                'coffee_shop_id' => $coffeeShop2->id,
            ],
            [
                'name' => 'Lupica E7',
                'model' => 'Lupica E7',
                'notes' => 'Espresso grinder with programmable settings',
                'coffee_shop_id' => $coffeeShop2->id,
            ],

            // Coffee Shop 3
            [
                'name' => 'Stainless Burr',
                'model' => 'Niche Stainless Burr',
                'notes' => 'Precision single dosing grinder',
                'coffee_shop_id' => $coffeeShop3->id,
            ],
            [
                'name' => 'Mazzer Mini',
                'model' => 'Mazzer Mini',
                'notes' => 'Backup espresso grinder',
                'coffee_shop_id' => $coffeeShop3->id,
            ],
        ];

        foreach ($grinders as $grinderData) {
            Grinder::create($grinderData);
        }

        // Get created records for use in sessions and shots
        $allBeans = Bean::all();
        $allGrinders = Grinder::all();
        $allUsers = User::all();

        // Create sample calibration sessions
        $sessions = [];
        for ($i = 0; $i < 15; $i++) {
            $randomBean = $allBeans->random();
            $randomGrinder = $allGrinders->random();
            $randomUser = $allUsers->random();

            $session = CalibrationSession::create([
                'bean_id' => $randomBean->id,
                'grinder_id' => $randomGrinder->id,
                'user_id' => $randomUser->id,
                'coffee_shop_id' => $randomBean->coffee_shop_id,
                'session_date' => now()->subDays(rand(1, 30)),
                'notes' => "Calibration session for {$randomBean->name}",
            ]);

            $sessions[] = $session;
        }

        // Create sample shots for each session
        foreach ($sessions as $session) {
            // Each session will have 3-8 shots
            $shotCount = rand(3, 8);
            for ($shotNum = 1; $shotNum <= $shotCount; $shotNum++) {
                // Generate realistic shot parameters
                $dose = 18.0 + (rand(-20, 20) / 10); // 16.0 to 20.0
                $yield = $dose * (rand(18, 24) / 10); // 1.8x to 2.4x ratio
                $time = rand(20, 35); // 20 to 35 seconds
                $grindSetting = rand(8, 15) . '.' . rand(0, 9); // Grinder setting like 12.5

                Shot::create([
                    'calibration_session_id' => $session->id,
                    'shot_number' => $shotNum,
                    'grind_setting' => $grindSetting,
                    'dose' => $dose,
                    'yield' => $yield,
                    'time_seconds' => $time,
                    'taste_notes' => $this->generateTasteNotes(),
                    'action_taken' => $this->generateActionTaken($shotNum),
                ]);
            }
        }
    }

    private function generateTasteNotes()
    {
        $notes = [
            'Balanced with notes of chocolate and caramel',
            'Bright acidity with citrus notes',
            'Smooth and creamy with nutty undertones',
            'Fruity with berry-like characteristics',
            'Floral and delicate with tea-like finish',
            'Full body with chocolate and spice',
            'Clean and bright with honey sweetness',
            'Complex with wine-like characteristics',
            'Nutty and sweet with caramel finish',
            'Earthy with herbal undertones',
        ];

        return $notes[array_rand($notes)];
    }

    private function generateActionTaken($shotNumber)
    {
        if ($shotNumber === 1) {
            return 'Initial shot - baseline for calibration';
        }

        $actions = [
            'Adjusted grind finer',
            'Adjusted grind coarser',
            'Increased dose',
            'Decreased dose',
            'Changed extraction time',
            'Modified tamping pressure',
            'Changed water temperature',
            'Adjusted pre-infusion time',
            'No changes needed',
            'Tweaked grind setting slightly',
        ];

        return $actions[array_rand($actions)];
    }
}
