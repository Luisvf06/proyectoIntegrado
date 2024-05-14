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
    public function generarFranjas(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            foreach ($data as $franja) {
                $codigo = $franja->xpathValue('column[@name="franja_cod"]')->sole();
                $descripcion = $franja->xpathValue('column[@name="descripcion"]')->sole();
                $horaDesde = $franja->xpathValue('column[@name="hora_desde"]')->sole();
                $horaHasta = $franja->xpathValue('column[@name="hora_hasta"]')->sole();

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
            return response()->json(['success' => 'Franjas creadas correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }
    }
}
