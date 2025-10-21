<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pet;
use App\Models\PetPhoto;

class PetPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pet = Pet::first();

        PetPhoto::create([
            'pet_id' => $pet->id,
            'file_path' => '"C:\Users\deivi\Desktop\Deimos\03\IMG_5527.jpg"',
        ]);

        PetPhoto::create([
            'pet_id' => $pet->id,
            'file_path' => '"C:\Users\deivi\Desktop\Deimos\03\IMG_5527.jpg"',
        ]);
    }
}
