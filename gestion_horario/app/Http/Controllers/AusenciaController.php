<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Horario;
use App\Models\User;
use App\Models\Ausencia;
use App\Http\Requests\AusenciaRequest;
use Illuminate\Http\JsonResponse;
class AusenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():JsonResponse
    {
        $ausencias= Ausencia::all();
        return  response()->json($ausencias, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    //en este caso la funcion store hace las funciones de store y create
    /**
     * Show the form for creating a new resource.
     */
    public function store(AusenciaRequest $request):JsonResponse
    {
        try {
            Ausencia::create($request->all());
            return response()->json(['message' => 'Ausencia creada correctamente'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show($id):JsonResponse
    {
        $ausencia= Ausencia::find($id);
        return  response()->json($ausencia, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Ausencia $ausencia)
    // {
         //return view('ausencia.edit',compact('ausencia')); en este caso no se necesita porque es para mostrar formulario y eso lo hace el cliente
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(AusenciaRequest $request, $id):JsonResponse
    {
        $ausencia=Ausencia::find($id);
        $ausencia->fecha=$request->fecha;//se sustituye el valor de fecha actual por el de la request
        $ausencia->hora=$request->hora;
        $ausencia->save();
        return response()->json(['message' => 'Ausencia actualizada correctamente'], 200);
    }

    public function destroy($id)
    {
        $ausencia=Ausencia::find($id)->delete();
        return response()->json(['message' => 'Ausencia eliminada correctamente'], 200);

}
}