<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use PDF;
use App\Models\Ausencia;
use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Horario;
use Carbon\Carbon;

class PDFController extends Controller
{
    public function generatePDF()
    {
        $today = Carbon::now()->format('d-m-Y');
        $todayWeek = Carbon::now()->dayOfWeek;
        
        $weekDays = [
            1 => 'l',
            2 => 'm',
            3 => 'x',
            4 => 'j',
            5 => 'v',
        ];
        
        $todayWeekLetter = $weekDays[$todayWeek] ?? 'No laboral';

        $ausencias = Ausencia::where('fecha', $today)->get();
        $data = [
            'title' => 'Lista de ausencias de hoy, ' . $today,
            'date' => $today,
            'ausencias' => [],
        ];

        foreach ($ausencias as $ausencia) {
            $horario = Horario::where('dia', $todayWeekLetter)->where('user_id', $ausencia->user_id)->first();
            $data['ausencias'][] = [
                'ausencia' => $ausencia,
                'horario' => $horario,
                'aula' => $horario ? Aula::find($horario->aula_id) : null,
                'grupo' => $horario ? Grupo::find($horario->grupo_id) : null,
            ];
        }

        $pdf = PDF::loadView('pdf.myPDF', $data);

        return $pdf->download('ausencias_hoy.pdf');
    }
}
