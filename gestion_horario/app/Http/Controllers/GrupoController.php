<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{
    public function insertGrupos(array $grupos)
    {
        Log::info('Datos de grupos recibidos:', ['grupos' => $grupos]);

        foreach ($grupos as $grupo) {
            Grupo::create([
                'grupo_cod' => $grupo[0],
                'descripcion' => $grupo[1]
            ]);
        }
    }
}

