<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Asignatura; 

class AsignaturaController extends Controller
{
    // Creación de asignaturas
    public function generarAsignaturas(Request $request) {
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        $asignaturas = $reader->xpathValue('//asignaturas');

        DB::beginTransaction();
        try {
            foreach ($asignaturas as $asignatura) {
                $codigo = $asignatura->xpathValue('asignatura_cod')->get();
                $descripcion = $asignatura->xpathValue('descripcion')->get();
        
                if (!Asignatura::where('codigo', $codigo)->exists()) {
                    Asignatura::create([
                        'codigo' => $codigo,
                        'descripcion' => $descripcion
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Manejar la excepción según sea necesario
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Asignaturas creadas correctamente'], 200);
    }
}
