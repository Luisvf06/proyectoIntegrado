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
                'name' => 'luis',
                'email' => 'luis.vazquez.franco.al@iespoligonosur.org',
                'user_name' => 'luisvaz',
                'password' => Hash::make('admin1')
            ],
            [
                'name' => 'jorge',
                'email' => 'jorbarab@ejemplo.com',
                'user_name' => 'jorbarab',
                'password' => Hash::make('profesor1')
            ],
            [
                'name' => 'carlos',
                'email' => 'cargarvil@ejemplo.com',
                'user_name' => 'cargarvil',
                'password' => Hash::make('profesor1')
            ],
            [
                'name' => 'joaquinma',
                'email' => 'monterona@ejemplo.com',
                'user_name' => 'monterona',
                'password' => Hash::make('profesor1')
            ],
            [
                'name' => 'mercedes',
                'email' => 'mflober@ejemplo.com',
                'user_name' => 'mflober',
                'password' => Hash::make('profesor1')
            ],
            [
                'name' => 'juan',
                'email' => 'juagommay@ejemplo.com',
                'user_name' => 'juagonmay',
                'password' => Hash::make('profesor1')
            ]
        ];

         foreach ($users as $user) {
            $createdUser = User::create($user);
            $token = $createdUser->createToken('API Token')->plainTextToken;
            echo "Token for {$createdUser->name}: $token\n";
        } 
    }
}
