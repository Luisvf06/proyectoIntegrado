<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class XMLController extends Controller
{
    public function uploadXML(Request $request)
    {
        $request->validate([
            'xml' => 'required|file|mimes:xml'
        ]);

        $xmlFile = $request->file('xml');
        $xmlContent = file_get_contents($xmlFile->getRealPath());

        // Procesar el XML para usuarios
        app(UserController::class)->generarUsers(new Request(['xml' => $xmlFile]));
        app(GrupoController::class)->generarGrupos(new Request(['xml' => $xmlFile]));
        app(AsignaturaController::class)->generarAsignaturas(new Request(['xml' => $xmlFile]));
        app(AulaController::class)->generarAulas(new Request(['xml' => $xmlFile]));
        app(PeriodoController::class)->generarPeriodos(new Request(['xml' => $xmlFile]));
        app(FranjaController::class)->generarFranjas(new Request(['xml' => $xmlFile]));
        app(HorarioController::class)->generarHorarios(new Request(['xml' => $xmlFile]));

        return back()->with('success', 'El archivo XML ha sido procesado correctamente.');
    }
}
