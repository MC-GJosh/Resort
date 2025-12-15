<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courts = [
            [
                'name' => 'Court 1',
                'rate' => 150.00,
                'location' => 'Left Side',
                'surface' => 'Standard Hard',
                'description' => 'Well-maintained court perfect for recreational and competitive play.',
                'image' => '/pickballcourtsolo.jpg',
                'features' => ['Net Included', 'Good Lighting', 'Shaded Area'],
                'is_active' => true,
            ],
            [
                'name' => 'Court 2',
                'rate' => 150.00,
                'location' => 'Left Middle',
                'surface' => 'Standard Hard',
                'description' => 'Well-maintained court perfect for recreational and competitive play.',
                'image' => '/pickballcourtsolo.jpg',
                'features' => ['Net Included', 'Good Lighting', 'Shaded Area'],
                'is_active' => true,
            ],
            [
                'name' => 'Court 3',
                'rate' => 150.00,
                'location' => 'Right Middle',
                'surface' => 'Standard Hard',
                'description' => 'Well-maintained court perfect for recreational and competitive play.',
                'image' => '/pickballcourtsolo.jpg',
                'features' => ['Net Included', 'Good Lighting', 'Shaded Area'],
                'is_active' => true,
            ],
            [
                'name' => 'Court 4',
                'rate' => 150.00,
                'location' => 'Right Side',
                'surface' => 'Premium Hard',
                'description' => 'Well-maintained court perfect for recreational and competitive play.',
                'image' => '/pickballcourtsolo.jpg',
                'features' => ['Net Included', 'Premium Lighting', 'Shaded Area', 'VIP Seating'],
                'is_active' => true,
            ],
        ];

        foreach ($courts as $court) {
            Court::create($court);
        }
    }
}
