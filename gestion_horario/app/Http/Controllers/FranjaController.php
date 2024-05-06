<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Franja;

class FranjaController extends Controller
{
    //Creación de franjas
    public function generarFranjas(Request $request) {
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        $franjas = $reader->xpathValue('//franjas');

        DB::beginTransaction();
        try {
            foreach ($franjas as $franja) {
                $codigo = $franja->xpathValue('franja_cod')->get();
                $descripcion = $franja->xpathValue('descripcion')->get();
                $horaDesde = $franja->xpathValue('hora_desde')->get();
                $horaHasta = $franja->xpathValue('hora_hasta')->get();
        
                if (!Franja::where('codigo', $codigo)->exists()) {
                    Franja::create([
                        'codigo' => $codigo,
                        'descripcion' => $descripcion,
                        'hora_desde' => $horaDesde,
                        'hora_hasta' => $horaHasta
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Manejar la excepción según sea necesario
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Franjas creadas correctamente'], 200);
    }
}
