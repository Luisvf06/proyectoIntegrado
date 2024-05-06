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
    public function generarAulas(Request $request) {
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        $aulas = $reader->xpathValue('//aulas');

        DB::beginTransaction();
        try {
            foreach ($aulas as $aula) {
                $codigo = $aula->xpathValue('aula_cod')->get();
                $descripcion = $aula->xpathValue('descripcion')->get();
        
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
