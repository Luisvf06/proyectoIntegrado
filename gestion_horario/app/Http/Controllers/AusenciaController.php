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
            // Log para verificar los datos validados
            Log::info('Datos validados recibidos en store:', $request->all());
    
            $data = $request->validated();
    
            // Log para verificar los datos después de la validación
            Log::info('Datos después de la validación:', $data);
    
            if (isset($data['fechas'])) {
                foreach ($data['fechas'] as $fecha) {
                    // Log para verificar cada fecha antes de formatear
                    Log::info('Procesando fecha:', ['fecha' => $fecha]);
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
    
                    // Log para verificar el formato de la fecha después de formatear
                    Log::info('Fecha formateada:', ['fecha' => $formattedDate]);
    
                    Ausencia::create([
                        'user_id' => $data['user_id'],
                        'fecha' => $formattedDate,
                        'hora' => $data['hora'] ?? null,
                    ]);
    
                    // Log para confirmar que se ha creado una ausencia
                    Log::info('Ausencia creada:', [
                        'user_id' => $data['user_id'],
                        'fecha' => $formattedDate,
                        'hora' => $data['hora'] ?? null,
                    ]);
                }
            } else {
                // Log para verificar la fecha antes de formatear
                Log::info('Procesando única fecha:', ['fecha' => $data['fecha']]);
                $formattedDate = Carbon::createFromFormat('d/m/Y', $data['fecha'])->format('Y-m-d');
    
                // Log para verificar el formato de la fecha después de formatear
                Log::info('Fecha formateada:', ['fecha' => $formattedDate]);
    
                Ausencia::create([
                    'user_id' => $data['user_id'],
                    'fecha' => $formattedDate,
                    'hora' => $data['hora'] ?? null,
                ]);
    
                // Log para confirmar que se ha creado una ausencia
                Log::info('Ausencia creada:', [
                    'user_id' => $data['user_id'],
                    'fecha' => $formattedDate,
                    'hora' => $data['hora'] ?? null,
                ]);
            }
    
            return response()->json(['message' => 'Ausencia creada correctamente'], 201);
        } catch (Exception $e) {
            // Log para capturar el mensaje de error completo
            Log::error('Error al crear la ausencia: '.$e->getMessage(), [
                'exception' => $e,
                'data' => $request->all(),
            ]);
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

    public function update(Request $request, $id): JsonResponse
{
    Log::info('Iniciando actualización de ausencia con ID: ' . $id);
    $ausencia = Ausencia::find($id);

    if (!$ausencia) {
        Log::error('Ausencia no encontrada con ID: ' . $id);
        return response()->json(['error' => 'Ausencia no encontrada'], 404);
    }

    try {
        if ($request->has('fecha')) {
            // Convertir la fecha al formato correcto
            $fecha = \DateTime::createFromFormat('d/m/Y', $request->fecha);
            if (!$fecha) {
                Log::error('Formato de fecha inválido: ' . $request->fecha);
                return response()->json(['error' => 'Formato de fecha inválido'], 400);
            }

            // Verificar que la fecha no sea anterior a la fecha actual
            $fechaActual = new \DateTime();
            if ($fecha < $fechaActual->setTime(0, 0, 0)) {
                Log::error('La fecha proporcionada es anterior a la fecha actual: ' . $request->fecha);
                return response()->json(['error' => 'La fecha no puede ser anterior a la fecha actual'], 400);
            }

            $ausencia->fecha = $fecha->format('Y-m-d');
        }

        if ($request->has('hora')) {
            $ausencia->hora = $request->hora;
        }

        $ausencia->save();

        Log::info('Ausencia actualizada correctamente');
        return response()->json(['message' => 'Ausencia actualizada correctamente', 'data' => $ausencia], 200);
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

    public function getAusenciasHoy(): JsonResponse
    {
        try {
            $hoy = Carbon::now()->format('Y-m-d');
            Log::info('Fecha de hoy ajustada: ' . $hoy);
    
            // Obtener las ausencias de todos los usuarioshoy
            $ausencias = Ausencia::with(['user.horarios.aula:id,descripcion', 'user.horarios.grupo:id,descripcion'])
                                ->whereDate('fecha', $hoy)
                                ->get(['id', 'user_id', 'fecha', 'hora']);
    
            Log::info('Ausencias obtenidas: ' . $ausencias->count());
            Log::info('Ausencias datos: ' . $ausencias->toJson());
    
            // Formatear para incluir el user_name, aula y grupo
            $ausenciasConDetalles = $ausencias->map(function($ausencia) {
                $horarios = $ausencia->user->horarios;
                Log::info('Horarios para usuario ' . $ausencia->user_id . ': ' . $horarios->toJson());
    
                return $horarios->map(function($horario) use ($ausencia) {
                    return [
                        'id' => $ausencia->id,
                        'user_id' => $ausencia->user_id,
                        'user_name' => $ausencia->user->name,
                        'fecha' => $ausencia->fecha,
                        'hora' => $ausencia->hora,
                        'aula_descripcion' => $horario->aula->descripcion,
                        'grupo_descripcion' => $horario->grupo->descripcion,
                    ];
                });
            })->flatten();
    
            Log::info('Ausencias con detalles: ' . $ausenciasConDetalles->toJson());
    
            return response()->json($ausenciasConDetalles, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener las ausencias de hoy: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ausencias de hoy: ' . $e->getMessage()], 500);
        }
    }
    

}
