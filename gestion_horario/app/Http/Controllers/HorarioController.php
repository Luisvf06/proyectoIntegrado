<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\User;
use App\Models\Aula;
use App\Models\Franja;
use App\Models\Grupo;
use App\Models\Asignatura;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HorarioController extends Controller
{
    public function insertHorarios(array $horarios)
    {
        Log::info('Datos de horarios recibidos:', ['horarios' => $horarios]);

        foreach ($horarios as $horario) {
            $profesor = User::where('professor_cod', $horario[1])->firstOrFail();
            $aula = Aula::where('aula_cod', $horario[5])->firstOrFail();
            $franja = Franja::where('codigo', $horario[3])->firstOrFail();
            $grupo = Grupo::where('grupo_cod', $horario[6])->firstOrFail();
            $asignatura = Asignatura::where('asignatura_cod', $horario[4])->firstOrFail();
            $periodo = Periodo::where('periodo_cod', $horario[7])->firstOrFail();

            Horario::create([
                'user_id' => $profesor->id,
                'aula_id' => $aula->id,
                'franja_id' => $franja->id,
                'grupo_id' => $grupo->id,
                'asignatura_id' => $asignatura->id,
                'periodo_id' => $periodo->id
            ]);
        }
    }
}

