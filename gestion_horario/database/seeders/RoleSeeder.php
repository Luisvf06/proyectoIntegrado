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
            'id'=>1,
            'name'=>'admin'
        ]);
        
        Role::create([
            'id'=>2,
            'name'=>'director'
        ]);
        
        Role::create([
            'id'=>3,
            'name'=>'jefatura'
        ]);
        
        Role::create([
            'id'=>4,
            'name'=>'docente'
        ]);
        DB::table('role_user')->insert([
            'role_id'=> 1,
            'user_id'=> 1,
            'added_by'=> 'luis'
        ]);
        DB::table('role_user')->insert([
            'role_id'=> 2,
            'user_id'=> 2,
            'added_by'=> 'luis'
        ]);
        DB::table('role_user')->insert([
            'role_id'=> 3,
            'user_id'=> 3,
            'added_by'=> 'pepepe'
        ]);
        DB::table('role_user')->insert([
            'role_id'=> 4,
            'user_id'=> 4,
            'added_by'=> 'pepepe'
        ]);
    }
}
