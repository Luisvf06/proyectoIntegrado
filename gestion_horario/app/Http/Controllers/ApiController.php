<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function register(){
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $xml = simplexml_load_file($request->file('file')->path());
            foreach ($xml->elemento as $item) {
                $nuevoRegistro = new Modelo([
                    'atributo1' => $item->atributo1,
                    'atributo2' => $item->atributo2,
                    // más atributos según tu estructura
                ]);
                $nuevoRegistro->save();
            }
            return response()->json(['status' => 'Procesado correctamente']);
        } else {
            return response()->json(['status' => 'Error en la subida del archivo'], 400);
        }
        
    }
}
