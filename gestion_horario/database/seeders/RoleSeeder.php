<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin',
            'id' => '1'
        ]);

        Role::create([
            'name' => 'director',
            'id' => '2'
        ]);

        Role::create([
            'name' => 'jefatura',
            'id' => '3'
        ]);

        Role::create([
            'name' => 'docente',
            'id' => '4'
        ]);
    
    DB::table('role_user')->insert([
        'role_id' => '1',
        'user_id' => '1'
    ]);
    DB::table('role_user')->insert([
        'role_id' => '2',
        'user_id' => '2'
    ]);
    DB::table('role_user')->insert([
        'role_id' => '3',
        'user_id' => '3'
    ]);
}
}