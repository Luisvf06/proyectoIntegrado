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
    /**
     * Guarda los usuarios a partir del XML procesado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->input('data');
        \Log::info('Datos recibidos en UserController:', ['data' => $data]);

        // Por defecto doy el rol profesor a todos
        $roleProfesorado = Role::where('name', 'profesorado')->first();
        // Verificar si el rol existe
        if (!$roleProfesorado) {
            return response()->json(['error' => 'Rol de "profesorado" no encontrado.'], 404);
        }

        // Iniciar una lista de usuarios creados para la respuesta
        $createdUsers = [];

        foreach ($data as $user) {
            \Log::info('Procesando usuario:', ['usuario' => $user]);
            // Primer nombre del profesor
            $fullName = $user->xpathValue('column[@name="nombre"]')->sole();
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0];

            // Uso el primer nombre y el año actual para generar una contraseña
            $password = Hash::make($firstName . Carbon::now()->year);

            // Obtengo el nombre completo del profesor
            $name = $user->xpathValue('column[@name="nombre"]')->sole();

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
            } else {
                $email = $this->normalizarTexto($firstName) . "2024@iespoligonosur.org";
            }

            $user_name = '';
            if (count($partesFiltradas) >= 2) {
                $user_name = $this->normalizarTexto($partesFiltradas[0]) . $this->normalizarTexto($partesFiltradas[1]);
            } else {
                $user_name = $this->normalizarTexto($firstName);
            }

            \Log::info('Creando nuevo usuario:', ['name' => $name, 'email' => $email, 'username' => $user_name]);

            // Crear un nuevo objeto User con valores predeterminados para ciertos atributos
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'user_name' => $user_name,
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
        return response()->json(['success' => true, 'created_users' => $createdUsers, 'token' => $createdUsers[0]->createToken("API TOKEN")->plainTextToken], 201);
    }

    /**
     * Normaliza el texto a minúsculas y reemplaza caracteres especiales.
     *
     * @param string $texto
     * @return string
     */
    protected function normalizarTexto($texto)
    {
        $texto = strtolower($texto);
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $texto
        );
        return $texto;
    }
}
