<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; #Importación del controller desde su directorio
use App\Http\Controllers\AusenciaController;

/*Route::get('/', function () {
    return view('welcome');
});
*/
//Las rutas del crud siempre llevan dos parametros por defecto, el primero es la ruta y el segundo es el controlador
//La ruta es siempre desde resources/views/
Route::view('/landing','landing.about')->name('hola');
#Route::view('/','index')->name('index');
Route::view('/about','about')->name('about');
Route::view('/services','services')->name('services');
Route::get('/',[UserController::class,'index'])->name('user.index');#el primer valor de la lista es el controlador y el segundo el de la funcion

Route::get('/create',[UserController::class,'create'])->name('user.create');


Route::get('/ausencia',[AusenciaController::class,'index'])->name('ausencia.index');
#Create
Route::post('/ausencia/create',[AusenciaController::class, 'create'])->name('ausencia.create');
Route::get('/ausencia/formulario',[AusenciaController::class,'formulario'])->name('ausencia.formulario');

#update
Route::get('/ausencia/edit/{ausencia}',[AusenciaController::class,'edit'])->name('ausencia.edit');
Route::put('/ausencia/update/{ausencia',[AusenciaController::class,'update'])->name('ausencia.update');
Route::get('/ausencia/show/{ausencia}',[AusenciaController::class,'show'])->name('ausencia.show');

#delete
Route::delete('/ausencia/destroy/{ausencia',[AusenciaController::class,'destroy'])->name('ausencia.destroy');

#Rutas resources
Route::resource('/post',AulaController::class);