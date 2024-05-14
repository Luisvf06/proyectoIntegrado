<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;//biblioteca para leer xml
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Asignatura; 


class AsignaturaController extends Controller
{
    public function generarAsignaturas(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            foreach ($data as $asignatura) {
                $codigo = $asignatura->xpathValue('column[@name="asignatura_cod"]')->sole();
                $descripcion = $asignatura->xpathValue('column[@name="descripcion"]')->sole();

                if (!Asignatura::where('codigo', $codigo)->exists()) {
                    Asignatura::create([
                        'codigo' => $codigo,
                        'descripcion' => $descripcion
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Asignaturas creadas correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }
    }
}
