<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Pet;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $room = Room::first();

        Pet::create([
            'name' => 'Buddy',
            'species' => 'Dog',
            'breed' => 'Labrador',
            'age' => 3,
            'status' => 'available',
            'room_id' => $room->id,
        ]);

        Pet::create([
            'name' => 'Mittens',
            'species' => 'Cat',
            'breed' => 'Persian',
            'age' => 2,
            'status' => 'available',
            'room_id' => $room->id,
        ]);
    }
}
