<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Standard Twin',
                'slug' => 'standard',
                'price' => 3500.00,
                'capacity' => 2,
                'size' => '25 sqm',
                'description' => 'A cozy retreat featuring twin beds, perfect for friends or solo travelers.',
                'amenities' => ['Free Wi-Fi', 'Hot Shower', 'Cable TV', 'Coffee Maker'],
                'image' => null,
                'image_class' => 'img-standard',
                'is_active' => true,
            ],
            [
                'name' => 'Deluxe King',
                'slug' => 'deluxe',
                'price' => 5500.00,
                'capacity' => 2,
                'size' => '35 sqm',
                'description' => 'Spacious room with a king-sized bed and a private balcony with garden views.',
                'amenities' => ['King Bed', 'Balcony', 'Work Desk', 'Mini Bar', 'Rain Shower'],
                'image' => null,
                'image_class' => 'img-deluxe',
                'is_active' => true,
            ],
            [
                'name' => 'Executive Family Suite',
                'slug' => 'suite',
                'price' => 8500.00,
                'capacity' => 4,
                'size' => '60 sqm',
                'description' => 'Our premium suite with a separate living area, kitchenette, and bathtub.',
                'amenities' => ['Living Room', 'Kitchenette', 'Bathtub', '2 Queen Beds', 'VIP Wi-Fi'],
                'image' => null,
                'image_class' => 'img-suite',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
