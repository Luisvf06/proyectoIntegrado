<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\User; 
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
{
    $users=User::all();
    return response()->json($users, 200);
    //return new UserCollection(User::latest()->paginate());
}
    public function show($id)
    {
        $user= User::find($id);
        return  response()->json($user, 200);
    }

    public function update(UserRequest $request, $id)
    {
        $user=User::find($id);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->professor_cod=$request->professor_cod;
        $user->password=$request->password;
        $user->user_name=$request->user_name;
        $user->rol=$request->rol;
        $user->save();
        return response()->json(['message' => 'Ausencia actualizada correctamente'], 200);
    }
    public function destroy($id)
    {
        $user=User::find($id)->delete();
        return response()->json(['message' => 'Ausencia eliminada correctamente'], 200);
    }
    /**
     * Retrieve a user by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getUser($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User found.',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }

    protected function normalizarTexto($texto) {
        $texto = strtolower($texto);
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $texto
        );
        return $texto;
    }

    public function store(UserRequest $request)
    {
        // Lee el archivo XML
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        // Por defecto doy el rol profesor a todos
        $roleProfesorado = Role::where('name', 'profesorado')->first();
        // Verificar si el rol existe
        if (!$roleProfesorado) {
            return response()->json(['error' => 'Rol de "profesorado" no encontrado.'], 404);
        }
        // Obtengo los datos de los usuarios
        $users = $reader->xpathValue('//profesores');
    
        // Iniciar una lista de usuarios creados para la respuesta
        $createdUsers = [];
    
        foreach ($users as $user) {
            // Primer nombre del profesor
            $fullName = $user->xpathValue('name')->get();
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0];
    
            // Uso el primer nombre y el año actual para generar una contraseña
            $password = $firstName . Carbon::now()->year;
            $password = Hash::make($password);
    
            // Obtengo el nombre completo del profesor
            $name = $user->xpathValue('nombre')->get();
    
            // Creo valores no válidos que puede contener el campo nombre para que no se incluyan
            $email_no_valido = ["del", "de la", "de los", "de las", "de"];
            // Creo un array con el nombre y apellidos, ambos contenidos en el campo nombre
            $partes = explode(' ', $name);
            // Filtro los nombres para que no incluyan los valores no válidos y que tengan una longitud mayor a 1 ya que hay un profesor que tiene una 'O' suelta
            $partesFiltradas = array_filter($partes, function ($parte) use ($email_no_valido) {
                $parteNormalizada = $this->normalizarTexto($parte);
                return !in_array($parteNormalizada, $email_no_valido) && strlen($parteNormalizada) > 1;
            });
            $email = '';
            if (count($partesFiltradas) >= 2) {
                // Se crea el correo usando las partes validas
                $email = $this->normalizarTexto(array_shift($partesFiltradas)) . '.' . $this->normalizarTexto(array_shift($partesFiltradas)) . '@iespoligonosur.org';
            }else{
                $email = $firstName . "2024@iespoligonosur.org";
            }
    
            $user_name = '';
            if (count($partesFiltradas) >= 2) {
                $user_name = $this->normalizarTexto($partesFiltradas[0]) . $this->normalizarTexto($partesFiltradas[1]);
            } else {
                $user_name = $this->normalizarTexto($firstName);
            }
    
            // Crear un nuevo objeto User con valores predeterminados para ciertos atributos
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'username' => $user_name,
                'rol' => 'profesor'
            ]);
    
            // Asignar el rol al usuario
            $newUser->roles()->attach($roleProfesorado->id);
            // Activar el usuario y guardar
            $newUser->active = true;
            $newUser->save();
    
            // Agregar al array de usuarios creados
            $createdUsers[] = $newUser;
        }
    
        // Retornar respuesta JSON
        return response()->json(['success' => true, 'created_users' => $createdUsers,'token'=>$user->createToken("API TOKEN")->plainTextToken], 201);
    }
    


}
