<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; #Importación del controller desde su directorio
use App\Http\Controllers\AuthController;
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

Route::post('/login',[AuthController::class,'loginUser']);
Route::middleware('auth:sanctum')->get('/user',function(Request $request){
    return $request->user();
});#esta ruta es para obtener el usuario autenticado, si no tiene token de autorización no se podrá acceder a esta ruta

Route::get('/login', function () {
        return view('login');
    })->name('login');


#Crea un grupo de rutas supervisadas por un mismo middleware
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/create',[AuthController::class,'createUser']);
    Route::get('/user/{id}', [UserController::class, 'getUser']);
});

/*
<?php

use Illuminate\Support\Facades\Route;

Route::post('/token', [
    'uses' => 'AccessTokenController@issueToken',
    'as' => 'token',
    'middleware' => 'throttle',
]);

Route::get('/authorize', [
    'uses' => 'AuthorizationController@authorize',
    'as' => 'authorizations.authorize',
    'middleware' => 'web',
]);

$guard = config('passport.guard', null);

Route::middleware(['web', $guard ? 'auth:'.$guard : 'auth'])->group(function () {
    Route::post('/token/refresh', [
        'uses' => 'TransientTokenController@refresh',
        'as' => 'token.refresh',
    ]);

    Route::post('/authorize', [
        'uses' => 'ApproveAuthorizationController@approve',
        'as' => 'authorizations.approve',
    ]);

    Route::delete('/authorize', [
        'uses' => 'DenyAuthorizationController@deny',
        'as' => 'authorizations.deny',
    ]);

    Route::get('/tokens', [
        'uses' => 'AuthorizedAccessTokenController@forUser',
        'as' => 'tokens.index',
    ]);

    Route::delete('/tokens/{token_id}', [
        'uses' => 'AuthorizedAccessTokenController@destroy',
        'as' => 'tokens.destroy',
    ]);

    Route::get('/clients', [
        'uses' => 'ClientController@forUser',
        'as' => 'clients.index',
    ]);

    Route::post('/clients', [
        'uses' => 'ClientController@store',
        'as' => 'clients.store',
    ]);

    Route::put('/clients/{client_id}', [
        'uses' => 'ClientController@update',
        'as' => 'clients.update',
    ]);

    Route::delete('/clients/{client_id}', [
        'uses' => 'ClientController@destroy',
        'as' => 'clients.destroy',
    ]);

    Route::get('/scopes', [
        'uses' => 'ScopeController@all',
        'as' => 'scopes.index',
    ]);

    Route::get('/personal-access-tokens', [
        'uses' => 'PersonalAccessTokenController@forUser',
        'as' => 'personal.tokens.index',
    ]);

    Route::post('/personal-access-tokens', [
        'uses' => 'PersonalAccessTokenController@store',
        'as' => 'personal.tokens.store',
    ]);

    Route::delete('/personal-access-tokens/{token_id}', [
        'uses' => 'PersonalAccessTokenController@destroy',
        'as' => 'personal.tokens.destroy',
    ]);
});
*/