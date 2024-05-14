<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Aula;


class AulaController extends Controller
{
    public function generarAulas(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            foreach ($data as $aula) {
                $codigo = $aula->xpathValue('column[@name="aula_cod"]')->sole();
                $descripcion = $aula->xpathValue('column[@name="descripcion"]')->sole();

                if (!Aula::where('codigo', $codigo)->exists()) {
                    Aula::create([
                        'codigo' => $codigo,
                        'descripcion' => $descripcion
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Aulas creadas correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }
    }
}