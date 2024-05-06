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
    // Creación de horarios
    public function generarHorarios(Request $request) {
        $xml = file_get_contents($request->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);
        $horarios = $reader->xpathValue('//horarios');

        DB::beginTransaction();
        try {
            foreach ($horarios as $horario) {
                $profesorCod = $horario->xpathValue('professor_cod')->get();
                $profesorId = User::where('codigo', $profesorCod)->first()->id;

                $aulaId = Aula::where('codigo', $horario->xpathValue('aula_cod')->get())->first()->id;
                $franjaId = Franja::where('codigo', $horario->xpathValue('franja_cod')->get())->first()->id;
                $grupoId = Grupo::where('codigo', $horario->xpathValue('grupo_cod')->get())->first()->id;
                $asignaturaId = Asignatura::where('codigo', $horario->xpathValue('asignatura_cod')->get())->first()->id;
                $periodoId = Periodo::where('codigo', $horario->xpathValue('periodo_cod')->get())->first()->id;

                Horario::create([
                    'profesor_id' => $profesorId,
                    'aula_id' => $aulaId,
                    'franja_id' => $franjaId,
                    'grupo_id' => $grupoId,
                    'asignatura_id' => $asignaturaId,
                    'periodo_id' => $periodoId
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo XML: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Horarios creados correctamente'], 200);
    }
}
