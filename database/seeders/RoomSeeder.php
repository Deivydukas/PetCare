<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shelter;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shelter = Shelter::first();

        Room::create([
            'name' => 'Mazuju kambarys',
            'capacity' => 5,
            'shelter_id' => $shelter->id,
        ]);

        Room::create([
            'name' => 'Didziuju kambarys',
            'capacity' => 3,
            'shelter_id' => $shelter->id,
        ]);
    }
}
