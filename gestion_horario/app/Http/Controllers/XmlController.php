<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class XmlController extends Controller
{
    public function uploadXML(Request $request)
    {
        $request->validate([
            'xml' => 'required|file|mimes:xml'
        ]);
    
        $xmlFile = $request->file('xml');
        $xmlContent = file_get_contents($xmlFile->getRealPath());
        // Procesar el XML para usuarios
        // En XmlController.php
$userController = new UserController();
$response = $userController->store($request);
return $response;

        app(GrupoController::class)->generarGrupos(new Request(['xml' => $xmlFile]));
        app(AsignaturaController::class)->generarAsignaturas(new Request(['xml' => $xmlFile]));
        app(AulaController::class)->generarAulas(new Request(['xml' => $xmlFile]));
        app(PeriodoController::class)->generarPeriodos(new Request(['xml' => $xmlFile]));
        app(FranjaController::class)->generarFranjas(new Request(['xml' => $xmlFile]));
        app(HorarioController::class)->generarHorarios(new Request(['xml' => $xmlFile]));

        return response()->json(['message' => 'El archivo XML ha sido procesado correctamente.']);
    }
}
