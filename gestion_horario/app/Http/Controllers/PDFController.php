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
    public function generatePDF(Request $request)
    {
        $selectedDate = $request->query('date');
        $date = Carbon::parse($selectedDate);
        $selectedDayOfWeek = $date->dayOfWeek;

        // Excluir domingos y sábados
        if ($selectedDayOfWeek == 0 || $selectedDayOfWeek == 6) {
            return response()->json(['message' => 'No se generan reportes de los fines de semana.'], 400);
        }

        $weekDays = [
            1 => 'L',
            2 => 'M',
            3 => 'X',
            4 => 'J',
            5 => 'V',
        ];

        $selectedWeekLetter = $weekDays[$selectedDayOfWeek] ?? 'No laboral';

        $ausencias = Ausencia::with([
                'user',
                'user.horarios.aula', 
                'user.horarios.grupo'
            ])
            ->whereDate('fecha', $date)
            ->get();

        $data = [
            'title' => 'Lista de ausencias del día, ' . $selectedDate,
            'date' => $selectedDate,
            'ausencias' => [],
        ];

        foreach ($ausencias as $ausencia) {
            $horario = $ausencia->user->horarios->firstWhere('dia', $selectedWeekLetter);
            $data['ausencias'][] = [
                'ausencia' => $ausencia,
                'horario' => $horario,
                'aula' => $horario ? $horario->aula : null,
                'grupo' => $horario ? $horario->grupo : null,
            ];
        }

        $pdf = PDF::loadView('pdf.myPDF', $data);

        return $pdf->download('ausencias_' . $selectedDate . '.pdf');
    }
}
