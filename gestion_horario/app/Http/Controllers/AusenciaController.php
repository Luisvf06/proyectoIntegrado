<?php

namespace App\Http\Controllers;

use App\Http\Requests\AusenciaRequest;
use App\Models\Ausencia;
use App\Models\Horario;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

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
            $ausencia = Ausencia::create($request->validated());
            return response()->json(['message' => 'Ausencia creada correctamente', 'ausencia' => $ausencia], 201);
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
            $ausencia->update($request->validated());
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
}
