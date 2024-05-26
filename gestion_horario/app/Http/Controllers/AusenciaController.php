<?php

namespace App\Http\Controllers;

use App\Http\Requests\AusenciaRequest;
use App\Models\Ausencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AusenciaMail;
class AusenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $ausencias = Ausencia::all();
        return response()->json($ausencias, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AusenciaRequest $request): JsonResponse
    {
        try {
            // Validar los datos
            $data = $request->validated();

            // Procesar los diferentes tipos de fecha
            if (isset($data['fechas'])) {
                // Caso de varias fechas
                foreach ($data['fechas'] as $fecha) {
                    // Convertir la fecha al formato correcto
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');

                    Ausencia::create([
                        'user_id' => $data['user_id'],
                        'fecha' => $formattedDate,
                        'hora' => $data['hora'] ?? null,
                    ]);
                }
            } else {
                // Caso de una sola fecha
                $formattedDate = Carbon::createFromFormat('d/m/Y', $data['fecha'])->format('Y-m-d');

                Ausencia::create([
                    'user_id' => $data['user_id'],
                    'fecha' => $formattedDate,
                    'hora' => $data['hora'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Ausencia creada correctamente'], 201);
        } catch (Exception $e) {
            Log::error('Error al crear la ausencia: '.$e->getMessage());
            return response()->json(['error' => 'Error al crear la ausencia: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        return response()->json($ausencia, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AusenciaRequest $request, $id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        try {
            $data = $request->validated();
            if (isset($data['fecha'])) {
                $data['fecha'] = Carbon::createFromFormat('d/m/Y', $data['fecha'])->format('Y-m-d');
            }
            if (isset($data['fechas'])) {
                $data['fechas'] = array_map(function($fecha) {
                    return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
                }, $data['fechas']);
            }
            $ausencia->update($data);
            return response()->json(['message' => 'Ausencia actualizada correctamente', 'ausencia' => $ausencia], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar la ausencia: '.$e->getMessage());
            return response()->json(['error' => 'Error al actualizar la ausencia: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        try {
            $ausencia->delete();
            return response()->json(['message' => 'Ausencia eliminada correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar la ausencia: '.$e->getMessage());
            return response()->json(['error' => 'Error al eliminar la ausencia: ' . $e->getMessage()], 500);
        }
    }

    public function sendMail(Request $request): JsonResponse
    {
      auth()->user()->ausencias()->create($request->all());
      Mail::to('luis@test.mail')->send(new AusenciaMail(''));
}
}
