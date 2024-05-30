<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function index(): JsonResponse
    {
        $ausencias = Ausencia::all(['id', 'fecha', 'hora', 'user_id']);
        return response()->json($ausencias, 200);
    }

    public function store(AusenciaRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if (isset($data['fechas'])) {
                foreach ($data['fechas'] as $fecha) {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
                    Ausencia::create([
                        'user_id' => $data['user_id'],
                        'fecha' => $formattedDate,
                        'hora' => $data['hora'] ?? null,
                    ]);
                }
            } else {
                $formattedDate = Carbon::createFromFormat('d/m/Y', $data['fecha'])->format('Y-m-d');
                Ausencia::create([
                    'user_id' => $data['user_id'],
                    'fecha' => $formattedDate,
                    'hora' => $data['hora'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Ausencia creada correctamente'], 201);
        } catch (Exception $e) {
            Log::error('Error al crear la ausencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear la ausencia: ' . $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        return response()->json($ausencia, 200);
    }

    public function update(AusenciaRequest $request, $id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        try {
            $data = $request->validated();

            if (isset($data['fecha'])) {
                $data['fecha'] = Carbon::createFromFormat('Y-m-d', $data['fecha'])->format('Y-m-d');
            }

            $ausencia->update($data);
            return response()->json(['message' => 'Ausencia actualizada correctamente', 'ausencia' => $ausencia], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar la ausencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la ausencia: ' . $e->getMessage()], 500);
        }
    }

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
            Log::error('Error al eliminar la ausencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la ausencia: ' . $e->getMessage()], 500);
        }
    }

    public function sendMail(Request $request): JsonResponse
    {
        auth()->user()->ausencias()->create($request->all());
        Mail::to('luis@test.mail')->send(new AusenciaMail(''));
        return response()->json(['message' => 'Mail sent successfully'], 200);
    }

    public function getUserAusencias(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $ausencias = $user->ausencias()->get(['id', 'fecha', 'hora']);
            return response()->json($ausencias, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener las ausencias: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ausencias: ' . $e->getMessage()], 500);
        }
    }
}

