<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'luis.vazquez.franco.al@iespoligonosur.org',
                'user_name' => 'luisvaz',
                'password' => Hash::make('admin1')
            ],
            [
                'name' => 'pepe',
                'email' => 'pepeperez@ejemplo.com',
                'user_name' => 'pepepe',
                'password' => Hash::make('papapa')
            ],
            [
                'name' => 'Carlos',
                'email' => 'carlosgar@ejemplo.com',
                'user_name' => 'cargar',
                'password' => Hash::make('cargar')
            ],
            [
                'name' => 'Jorge',
                'email' => 'jorgebar@ejemplo.com',
                'user_name' => 'jorbar',
                'password' => Hash::make('jorbar')
            ]
        ];

        /* foreach ($users as $user) {
            $createdUser = User::create($user);
            $token = $createdUser->createToken('API Token')->plainTextToken;
            echo "Token for {$createdUser->name}: $token\n";
        } */
    }
}
