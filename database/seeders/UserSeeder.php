<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Naudotojas',
            'email' => 'naudotojas@inbox.lt',
            'password' => bcrypt('slaptazodis'),
            'adress' => 'Vilnius, Lithuania',
            'role' => 'user',
        ]);
        User::create([
            'name' => 'Administratorius',
            'email' => 'adminas@inbox.lt',
            'password' => bcrypt('slaptazodis'),
            'adress' => 'Vilnius, Lithuania',
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Darbuotojas',
            'email' => 'darbuotojas@inbox.lt',
            'password' => bcrypt('slaptazodis'),
            'adress' => 'Vilnius, Lithuania',
            'role' => 'worker',
        ]);
    }
}
