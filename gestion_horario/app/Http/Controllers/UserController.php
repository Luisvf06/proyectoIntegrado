<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserMail;

class UserController extends Controller
{
    public function insertUsers(array $profesores)
    {
        Log::info('Datos de profesores recibidos:', ['profesores' => $profesores]);

        // Por defecto doy el rol profesor a todos
        $roleProfesorado = Role::firstOrCreate(['name' => 'docente']);

        // Iniciar una lista de usuarios creados para la respuesta
        $createdUsers = [];

        foreach ($profesores as $profesor) {
            Log::info('Procesando profesor:', ['profesor' => $profesor]);

            // Primer nombre del profesor
            $fullName = $profesor[1];
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0];

            // Generar una contraseña en texto plano para enviar por correo
            $plainPassword = $firstName . Carbon::now()->year;
            // Uso el primer nombre y el año actual para generar una contraseña
            $hashedPassword = Hash::make($plainPassword);

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

            // Genero el correo electrónico del profesor
            $email_base = '';
            if (count($partesFiltradas) >= 2) {
                $email_base = $this->normalizarTexto(array_shift($partesFiltradas)) . '.' . $this->normalizarTexto(array_shift($partesFiltradas));
            } else {
                $email_base = $this->normalizarTexto($firstName) . "2024";
            }
            $email = $email_base . '@iespoligonosur1234.org';

            // Verificar si el correo ya existe y modificarlo si es necesario
            $original_email = $email_base;
            $email_counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $original_email . $email_counter . '@iespoligonosur1234.org';
                $email_counter++;
            }

            // Genero el nombre de usuario del profesor
            $user_name = '';
            if (count($partesFiltradas) >= 2) {
                $user_name = $this->normalizarTexto($partesFiltradas[0]) . $this->normalizarTexto($partesFiltradas[1]);
            } else {
                $user_name = $this->normalizarTexto($firstName);
            }

            // Verificar si el username ya existe y modificarlo si es necesario
            $original_username = $user_name;
            $counter = 1;
            while (User::where('user_name', $user_name)->exists()) {
                $user_name = $original_username . $counter;
                $counter++;
            }

            Log::info('Creando nuevo usuario:', ['name' => $name, 'email' => $email, 'username' => $user_name]);

            try {
                // Crear un nuevo objeto User con valores predeterminados para ciertos atributos
                $newUser = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'user_name' => $user_name,
                    'professor_cod' => $profesor[0]
                ]);

                // Asignar el rol al usuario
                $newUser->roles()->attach($roleProfesorado->id);
                $newUser->save();

                // Enviar correo al usuario
                Mail::to($newUser->email)->send(new UserMail([
                    'name' => $name,
                    'email' => $email,
                    'user_name' => $user_name,
                    'password' => $plainPassword 
                ]));

                Log::info('Correo enviado a:', ['email' => $newUser->email]);

                // Agregar al array de usuarios creados
                $createdUsers[] = $newUser;
            } catch (\Exception $e) {
                Log::error('Error al crear usuario o enviar correo: ' . $e->getMessage());
                continue; // Saltar este usuario si hay un error
            }
        }

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
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->all());

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->roles()->detach();
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
    public function getAuthenticatedUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json($user, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user data: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
}
}


