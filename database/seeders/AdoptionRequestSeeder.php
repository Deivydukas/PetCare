<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pet;
use App\Models\AdoptionRequest;

class AdoptionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $pet = Pet::first();

        AdoptionRequest::create([
            // 'user_id' => $user->id,
            'pet_id' => $pet->id,
            'applicant_name' => 'John Doe',
            'email' => 'Jhondoe1@gmail.com',
            'status' => 'pending',
            'application_text' => 'I would love to adopt this pet because I have a big yard and lots of love to give.'
        ]);
    }
}
