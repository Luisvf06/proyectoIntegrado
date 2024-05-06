<?php

namespace App\Http\Controllers;
use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Ausencia;
use App\Models\Franja;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class xmlController extends Controller
{
    // Función para eliminar tildes y convertir a minúsculas
   





    public function registrar (Request $request){

        return redirect()->route('export');
    }


    public function datosXml(Request $request){
        DB::beginTransaction();
        try {
            $xml = file_get_contents($request->file('xml')->getRealPath());
            $reader = XmlReader::fromString($xml);
    
            $this->generarAsignaturas($reader);
            $this->generarAulas($reader);
            $this->generarFranjas($reader);
            $this->generarGrupos($reader);
            $this->generarHorarios($reader);
            $this->generarPeriodos($reader);
    
            DB::commit();
            return redirect()->route('registrar');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error al procesar el archivo XML: ' . $e->getMessage());
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


        return redirect()->route('generarUsers');
    }
}
}