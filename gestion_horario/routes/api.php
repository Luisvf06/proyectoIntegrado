<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; #Importación del controller desde su directorio
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AusenciaController;
use App\Http\Controllers\Api\LoginController;
use Illuminate\Http\Request;
use App\Http\Controllers\xmlController;
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

Route::post('/upload-xml', [XMLController::class, 'uploadXML']);

Route::resource('/users', UserController::class);//Para ver las rutas del resource usar en la terminal php artisan route:list
Route::resource('/ausencias', AusenciaController::class);
Route::resource('/asignaturas', AsignaturaController::class);
Route::resource('horarios', HorarioController::class);
Route::resource('aulas', AulaController::class);
Route::resource('franjas', FranjaController::class);
Route::resource('grupos', GrupoController::class);
Route::resource('periodos', PeriodoController::class);
