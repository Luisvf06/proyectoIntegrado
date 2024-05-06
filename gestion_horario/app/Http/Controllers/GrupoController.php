<?php

namespace App\Http\Controllers;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Grupo;

class GrupoController extends Controller
{
    public function generarGrupos(Request $request) {
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        $grupos = $reader->xpathValue('//grupos');

        DB::beginTransaction();
        try {
            foreach ($grupos as $grupo) {
                $codigo = $grupo->xpathValue('grupo_cod')->get();
                $descripcion = $grupo->xpathValue('descripcion')->get();
        
                if (!Grupo::where('codigo', $codigo)->exists()) {
                    Grupo::create([
                        'codigo' => $codigo,
                        'descripcion' => $descripcion
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Grupos creados correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }
    }
}
