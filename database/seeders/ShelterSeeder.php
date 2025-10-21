<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shelter;

class ShelterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shelter::create([
            'name' => 'Happy Paws Shelter',
            'address' => 'Vilnius, Lithuania',
            'phone' => '+37060012345',
            'email' => 'info@happypaws.lt',
        ]);

        Shelter::create([
            'name' => 'Animal Haven',
            'address' => 'Kaunas, Lithuania',
            'phone' => '+37061234567',
            'email' => 'contact@animalhaven.lt',
        ]);
    }
}
