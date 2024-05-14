<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Periodo;


class PeriodoController extends Controller
{
    public function generarPeriodos(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            foreach ($data as $periodo) {
                $codigo = $periodo->xpathValue('column[@name="periodo_cod"]')->sole();
                $descripcion = $periodo->xpathValue('column[@name="descripcion"]')->sole();
                $fechaDesde = $periodo->xpathValue('column[@name="fecha_desde"]')->sole();
                $fechaHasta = $periodo->xpathValue('column[@name="fecha_hasta"]')->sole();

                if (!Periodo::where('codigo', $codigo)->exists()) {
                    Periodo::create([
                        'codigo' => $codigo,
                        'descripcion' => $descripcion,
                        'fecha_desde' => $fechaDesde,
                        'fecha_hasta' => $fechaHasta
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Periodos creados correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }
    }
}