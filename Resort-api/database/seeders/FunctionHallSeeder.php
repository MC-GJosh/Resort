<?php

namespace Database\Seeders;

use App\Models\FunctionHall;
use Illuminate\Database\Seeder;

class FunctionHallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halls = [
            [
                'name' => 'The Pearl Suite',
                'slug' => 'small',
                'price_per_4hrs' => 15000.00,
                'min_capacity' => 30,
                'max_capacity' => 70,
                'size' => '120 sq. meters',
                'description' => 'A cozy, elegant atmosphere designed for close-knit gatherings, meetings, and private parties.',
                'amenities' => ['Basic Sound System', '1 Projector', 'Private Restroom', 'AC'],
                'image' => null,
                'image_class' => 'small-hall-img',
                'badge' => 'Intimate Choice',
                'is_premium' => false,
                'is_active' => true,
            ],
            [
                'name' => 'The Grand Ballroom',
                'slug' => 'big',
                'price_per_4hrs' => 45000.00,
                'min_capacity' => 150,
                'max_capacity' => 400,
                'size' => '500 sq. meters',
                'description' => 'Our flagship venue featuring high ceilings and luxurious decor for weddings and corporate galas.',
                'amenities' => ['Full Stage', 'Professional Lighting & Audio', 'Dressing Room', 'VIP Lounge'],
                'image' => null,
                'image_class' => 'big-hall-img',
                'badge' => 'Grand Choice',
                'is_premium' => true,
                'is_active' => true,
            ],
        ];

        foreach ($halls as $hall) {
            FunctionHall::create($hall);
        }
    }
}
