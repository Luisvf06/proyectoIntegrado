<?php

namespace App\Http\Controllers;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Grupo;


class GrupoController extends Controller
{
    public function generarGrupos(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            foreach ($data as $grupo) {
                $codigo = $grupo->xpathValue('column[@name="grupo_cod"]')->sole();
                $descripcion = $grupo->xpathValue('column[@name="descripcion"]')->sole();

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