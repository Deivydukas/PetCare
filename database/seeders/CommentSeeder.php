<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pet;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $pet = Pet::first();

        Comment::create([
            'user_name' => 'Sara',
            'pet_id' => $pet->id,
            'content' => 'Such a cute dog!',
        ]);

        Comment::create([
            'user_name' => 'Tomas',
            'pet_id' => $pet->id,
            'content' => 'Is he still available?',
        ]);

        Comment::create([
            'user_name' => 'Alina',
            'pet_id' => $pet->id,
            'content' => 'Guess not, I just adopted him!',
            'parent_id' => 2,
        ]);
    }
}
