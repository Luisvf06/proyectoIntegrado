<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::all();#para un filtro se usaria ::where en lugar de all y la condicion por ejemplo ('name','==','Jorge')->get(); el get es para obtener los resultados que superan el filtro
        return view('user.index', ["users"=>$users]);#users es la clave y $users el valor
        #return view('user.index',compact('users')); compact se usa para que la clave y el valor tengan el mismo nombre(como arriba) de forma que se simplifica el código
    }

    public function create(){
        $user= new User;
        $user->name='Vilches';
        $user->email='Vilches@ejemplo.es';
        $user->password=Hash::make('123456');
        $user->save();

        User::create([
            "name"=>"Car",
            "email"=>"Cars@ejemplo.es",
            "password"=>Hash::make('123654')
        ]);
        return redirect()->route('user.index');
    }

}
