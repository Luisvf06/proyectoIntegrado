<?php

namespace App\Http\Controllers;
use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Ausencia;
use App\Models\Franja;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Periodo;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;

class xmlController extends Controller
{
    // Función para eliminar tildes y convertir a minúsculas
    protected function normalizarTexto($texto) {
        $texto = strtolower($texto);
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $texto
        );
        return $texto;
    }

    public function datosXml (Request $request){
        // Lee el archivo XML
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);

        // Obtngo los datos de los usuarios
        $users = $reader->xpathValue('//profesores');

        foreach ($users as $user) {
            // Primer nombre del profesor
            $firstName = $user->xpathValue('name/first')->get();

            // Uso el primer nombre y el año actual para generar una contraseña
            $password = $firstName . Carbon::now()->year;
            $password = Hash::make($password);

            // Obtengo el nombre completo del profesor
            $name = $user->xpathValue('name')->get();

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
            }

            // Crear un nuevo objeto User con valores predeterminados para ciertos atributos
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            // Asignar el rol al usuario
            $newUser->roles()->attach($roleModel);

            // Activar el usuario y guardar
            $newUser->active = true;
            $newUser->save();
        }

        return redirect()->route('registrar');
    }

    public function registrar (Request $request){

        return redirect()->route('export');
    }

    public function export (Request $request){

        $users = User::with('roles')->get();

        $builder = XmlBuilder::create('users');

        foreach ($users as $user) {
            $builder->element('user', function (XmlBuilder $builder) use ($user) {
                $builder->element('name', $user->name);
                $builder->element('email', $user->email);

                $role = $user->roles->first();
                $builder->element('role', $role->name);
            });
        }
        $xml = $builder->build();
        file_put_contents('users.xml', $xml);


        return redirect()->route('datosXml');
    }
}
