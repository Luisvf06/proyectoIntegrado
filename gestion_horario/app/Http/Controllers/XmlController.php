<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\FranjaController;
use App\Http\Controllers\HorarioController;
use Saloon\XmlWrangler\XmlReader;

class XmlController extends Controller
{
    protected $userController;
    protected $grupoController;
    protected $asignaturaController;
    protected $aulaController;
    protected $periodoController;
    protected $franjaController;
    protected $horarioController;

    public function __construct(
        UserController $userController,
        GrupoController $grupoController,
        AsignaturaController $asignaturaController,
        AulaController $aulaController,
        PeriodoController $periodoController,
        FranjaController $franjaController,
        HorarioController $horarioController
    ) {
        $this->userController = $userController;
        $this->grupoController = $grupoController;
        $this->asignaturaController = $asignaturaController;
        $this->aulaController = $aulaController;
        $this->periodoController = $periodoController;
        $this->franjaController = $franjaController;
        $this->horarioController = $horarioController;
    }

    public function uploadXML(Request $request)
    {
        // Valida el  XML
        $request->validate([
            'xml' => 'required|file|mimes:xml'
        ]);

        // Carga el XML
        $xmlFile = $request->file('xml');
        $xmlContent = file_get_contents($xmlFile->getRealPath());

        // Procesa el contenido 
        $reader = XmlReader::fromString($xmlContent);

        $usersData = $reader->xpathValue('//table[@name="profesores"]/row')->get();
        $gruposData = $reader->xpathValue('//table[@name="grupos"]/row')->get();
        $asignaturasData = $reader->xpathValue('//table[@name="asignaturas"]/row')->get();
        $aulasData = $reader->xpathValue('//table[@name="aulas"]/row')->get();
        $periodosData = $reader->xpathValue('//table[@name="periodos"]/row')->get();
        $franjasData = $reader->xpathValue('//table[@name="franjas"]/row')->get();
        $horariosData = $reader->xpathValue('//table[@name="horarios"]/row')->get();

        //Logs
        \Log::info('Usuarios:', ['data' => $usersData]);
        \Log::info('Grupos:', ['data' => $gruposData]);
        \Log::info('Asignaturas:', ['data' => $asignaturasData]);
        \Log::info('Aulas:', ['data' => $aulasData]);
        \Log::info('Periodos:', ['data' => $periodosData]);
        \Log::info('Franjas:', ['data' => $franjasData]);
        \Log::info('Horarios:', ['data' => $horariosData]);

        //Llamada a los métodos de lso controladores con modelos para su registro
        $this->userController->store(new Request(['data' => $usersData]));
        $this->grupoController->generarGrupos(new Request(['data' => $gruposData]));
        $this->asignaturaController->generarAsignaturas(new Request(['data' => $asignaturasData]));
        $this->aulaController->generarAulas(new Request(['data' => $aulasData]));
        $this->periodoController->generarPeriodos(new Request(['data' => $periodosData]));
        $this->franjaController->generarFranjas(new Request(['data' => $franjasData]));
        $this->horarioController->generarHorarios(new Request(['data' => $horariosData]));

        return response()->json(['message' => 'El archivo XML ha sido procesado correctamente.']);
    }
}
