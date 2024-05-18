<?php

namespace App\Http\Controllers;

use App\Models\Franja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FranjaController extends Controller
{
    public function insertFranjas(array $franjas)
    {
        Log::info('Datos de franjas recibidos:', ['franjas' => $franjas]);

        foreach ($franjas as $franja) {
            Franja::create([
                'codigo' => $franja[0],
                'descripcion' => $franja[1],
                'hora_desde' => $franja[2],
                'hora_hasta' => $franja[3]
            ]);
        }
    }
}
