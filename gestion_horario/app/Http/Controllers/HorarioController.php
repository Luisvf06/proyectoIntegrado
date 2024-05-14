<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Saloon\XmlWrangler\XmlReader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\{Horario, User, Aula, Franja, Grupo, Asignatura, Periodo};

class HorarioController extends Controller
{
    public function generarHorarios(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            foreach ($data as $horario) {
                $profesorCod = $horario->xpathValue('column[@name="id"]')->sole();
                $profesor = User::where('codigo', $profesorCod)->first();
                if (!$profesor) {
                    throw new \Exception('Profesor no encontrado: ' . $profesorCod);
                }

                $aulaCod = $horario->xpathValue('column[@name="id"]')->sole();
                $aula = Aula::where('codigo', $aulaCod)->first();
                if (!$aula) {
                    throw new \Exception('Aula no encontrada: ' . $aulaCod);
                }

                $franjaCod = $horario->xpathValue('column[@name="id"]')->sole();
                $franja = Franja::where('codigo', $franjaCod)->first();
                if (!$franja) {
                    throw new \Exception('Franja no encontrada: ' . $franjaCod);
                }

                $grupoCod = $horario->xpathValue('column[@name="id"]')->sole();
                $grupo = Grupo::where('codigo', $grupoCod)->first();
                if (!$grupo) {
                    throw new \Exception('Grupo no encontrado: ' . $grupoCod);
                }

                $asignaturaCod = $horario->xpathValue('column[@name="id"]')->sole();
                $asignatura = Asignatura::where('codigo', $asignaturaCod)->first();
                if (!$asignatura) {
                    throw new \Exception('Asignatura no encontrada: ' . $asignaturaCod);
                }

                $periodoCod = $horario->xpathValue('column[@name="id"]')->sole();
                $periodo = Periodo::where('codigo', $periodoCod)->first();
                if (!$periodo) {
                    throw new \Exception('Periodo no encontrado: ' . $periodoCod);
                }

                Horario::create([
                    'user_id' => $profesor->id,
                    'aula_id' => $aula->id,
                    'franja_id' => $franja->id,
                    'grupo_id' => $grupo->id,
                    'asignatura_id' => $asignatura->id,
                    'periodo_id' => $periodo->id
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'Horarios creados correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }
    }
}
