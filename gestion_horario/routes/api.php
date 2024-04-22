<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/users',[ApiController::class,'register']);

Route::post('/api/upload', function (Request $request) {
    $xmlFile = $request->file('file');
    // Asegúrate de validar y almacenar el archivo adecuadamente
    // Procesar XML, etc.
    return response()->json(['status' => 'Archivo recibido']);
});