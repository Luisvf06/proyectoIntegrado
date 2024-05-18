<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Inserta usuarios basándose en los datos extraídos del XML.
     *
     * @param array $profesores
     * @return void
     */
    public function insertUsers(array $profesores)
    {
        Log::info('Datos de profesores recibidos:', ['profesores' => $profesores]);

        // Por defecto doy el rol profesor a todos
        $roleProfesorado = Role::where('name', 'profesorado')->first();
        // Verificar si el rol existe
        if (!$roleProfesorado) {
            Log::error('Rol de "profesorado" no encontrado.');
            return;
        }

        // Iniciar una lista de usuarios creados para la respuesta
        $createdUsers = [];

        foreach ($profesores as $profesor) {
            Log::info('Procesando profesor:', ['profesor' => $profesor]);
            
            // Primer nombre del profesor
            $fullName = $profesor[1];
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0];

            // Uso el primer nombre y el año actual para generar una contraseña
            $password = Hash::make($firstName . Carbon::now()->year);

            // Obtengo el nombre completo del profesor
            $name = $fullName;

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

            Log::info('Creando nuevo usuario:', ['name' => $name, 'email' => $email, 'username' => $user_name]);

            // Crear un nuevo objeto User con valores predeterminados para ciertos atributos
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'user_name' => $user_name,
                'professor_cod' => $profesor[0] // Asegúrate de que esto se ajuste a tu esquema de base de datos
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
