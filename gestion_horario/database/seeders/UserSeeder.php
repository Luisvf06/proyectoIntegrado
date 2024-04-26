<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id'=>1,
            'name'=>'admin',
            'email'=>'luis.vazquez.franco.al@iespoligonosur.org',
            'user_name'=>'luisvaz',
            'password'=>Hash::make('admin1')
        ]);
        User::create([
            'id'=>2,
            'name'=>'pepe',
            'email'=>'pepeperez@ejemplo.com',
            'user_name'=>'pepepe',
            'password'=>Hash::make('papapa')
        ]);
        User::create([
            'id'=>3,
            'name'=>'Carlos',
            'email'=>'carlosgar@ejemplo.com',
            'user_name'=>'cargar',
            'password'=>Hash::make('cargar')
        ]);
        User::create([
            'id'=>4,
            'name'=>'Jorge',
            'email'=>'jorgebar@ejemplo.com',
            'user_name'=>'jorbar',
            'password'=>Hash::make('jorbar')
        ]);
    }
}

