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
/*Route::get('/', function () {
    return view('welcome');
});
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Ruta para obtener el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getAuthenticatedUser']);



Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::resource('/users', UserController::class);//Para ver las rutas del resource usar en la terminal php artisan route:list
    Route::resource('/ausencias', AusenciaController::class);
    Route::resource('/asignaturas', AsignaturaController::class);
    Route::post('/upload-xml', [XmlController::class, 'uploadXML']);
    Route::resource('/horarios', HorarioController::class);
    Route::resource('/aulas', AulaController::class);
    Route::resource('/franjas', FranjaController::class);
    Route::resource('/grupos', GrupoController::class);
    Route::resource('/periodos', PeriodoController::class);
});