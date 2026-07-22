<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'rubenmachado76@gmail.com'], // criterio único
            [
                'name' => 'Ruben Machado',
                'password' => Hash::make('gooNet560'),
                'role' => 'admin',
            ]
        );
    }
}
