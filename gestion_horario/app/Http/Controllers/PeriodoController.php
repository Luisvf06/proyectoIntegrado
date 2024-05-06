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
    // Creación de periodos
    public function generarPeriodos(Request $request) {
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        $periodos = $reader->xpathValue('//periodos');

        DB::beginTransaction();
        try {
            foreach ($periodos as $periodo) {
                $codigo = $periodo->xpathValue('periodo_cod')->get();
                $descripcion = $periodo->xpathValue('descripcion')->get();
                $fechaDesde = $periodo->xpathValue('fecha_desde')->get();
                $fechaHasta = $periodo->xpathValue('fecha_hasta')->get();

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