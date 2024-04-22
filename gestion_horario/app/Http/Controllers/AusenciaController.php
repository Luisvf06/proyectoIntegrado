<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Ausencia;
use App\Http\Requests\AusenciaRequest;
class AusenciaController extends Controller
{
    public function index(): View
    {
        $ausencias=Ausencia::all();
        return view('ausencia.index',compact('ausencias'));
    }

    public function create(AusenciaRequest $request):RedirectResponse{
        /*$request->validate([
            'descripcion'=>'max:250'
            'horario_id'=>'required',
            'user_id'=>''
        ]);la validacion se puede hacer asi pero es mejor hacer una custom request*/
        $ausencia=New Ausencia;
        $ausencia->descripcion = $request->descripcion;
        $ausencia->horario_id=$request->horario;
        $ausencia->user_id=$request->profesor;
        $ausencia->save();
        return redirect()->route('ausencia.index')->with('success','Ausencia creada');
    }

    /*
    public function create(Request $request){
        Ausencia::create([
            'descripcion'=>$request->descripcion
        ]);
        return redirect()->route('ausencia.index');
    }
    */
    public function formulario() :View{
        return view('ausencia.formulario');  // Asegúrate de que el nombre de la vista coincida con el archivo de la vista que creaste.
    }

    public function edit(Ausencia $ausencia):View{
        return view('ausencia.edit',compact('ausencia'));
    }

    public function update(AusenciaRequest $request,Ausencia $ausencia) :RedirectResponse{
        $ausencia->update($request->all());
        return redirect()->route('ausencia.index')->with('success','Ausencia modificada');
    }
    
    public function show(Ausencia $ausencia):View{
        return view('ausencia.show',compact('ausencia'));
    }

    public function destroy(Request $request,Ausencia $ausencia):RedirectResponse{
        $ausencia->delete();
        return redirect()->route('ausencia.index')->with('danger','ausencia borrada');
    }
}
