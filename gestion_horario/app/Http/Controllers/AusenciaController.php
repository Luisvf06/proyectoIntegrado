<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AusenciaRequest;
class AusenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ausencias= Ausencia::all();
        //return 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AusenciaRequest $request):RedirectResponse
    {
        $ausencia=new Ausencia;
        $ausencia->fecha=$request->fecha;
        $ausencia->hora=$request->hora;
        $ausencia->profesor_id=$request->profesor_id;
        $ausencia->save();

        return redirect()->route('ausencias.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ausencia $ausencia)
    {
        //return view('ausencia.edit',compact('ausencia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AusenciaRequest $request, Ausencia $ausencia):RedirectResponse
    {
        $ausencia->update($request->all());
        return redirect()->route('ausencias.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Ausencia $ausencia)
    {
        $ausencia->delete();
        return redirect()->route('ausencias.index');
    }
}
