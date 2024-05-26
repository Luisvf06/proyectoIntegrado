<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; #Importación del controller desde su directorio
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AusenciaController;
use App\Http\Controllers\Api\V1\LoginController;
use Illuminate\Http\Request;
use App\Http\Controllers\XmlController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AulaController;    
use App\Http\Controllers\FranjaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PeriodoController;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserMail;
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getAuthenticatedUser']);

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/horario',[HorarioController::class, 'getUserHorario']);
    Route::resource('/users', UserController::class);//Para ver las rutas del resource usar en la terminal php artisan route:list
    Route::resource('/ausencias', AusenciaController::class);
    Route::resource('/asignaturas', AsignaturaController::class);
    Route::post('/upload-xml', [XmlController::class, 'uploadXML']);
    Route::resource('/horarios', HorarioController::class);
    Route::resource('/aulas', AulaController::class);
    Route::resource('/franjas', FranjaController::class);
    Route::resource('/grupos', GrupoController::class);
    Route::resource('/periodos', PeriodoController::class);
    Route::get('/send-test-email', function () {
        $user = \App\Models\User::first();
        Mail::to($user->email)->send(new UserMail([
            'name' => $user->name,
            'email' => $user->email,
            'user_name' => $user->user_name
        ]));
        return 'Email sent!';
    });
});