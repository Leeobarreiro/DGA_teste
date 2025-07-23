<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioDisponivel;
use Carbon\Carbon;

class HorarioController extends Controller
{
    public function horariosPorMaquina()
{
    $horarios = HorarioDisponivel::all();

    $dias = [
        'domingo' => 0,
        'segunda' => 1,
        'terca' => 2,
        'terça' => 2,
        'quarta' => 3,
        'quinta' => 4,
        'sexta' => 5,
        'sabado' => 6,
        'sábado' => 6,
    ];

    $dados = $horarios->groupBy('maquina_id')->map(function ($horarios) use ($dias) {
        return $horarios->map(function ($h) use ($dias) {
            $diaSemana = strtolower(trim($h->dia_semana));

            return [
                'daysOfWeek' => [$dias[$diaSemana] ?? null],
                'startTime' => $h->hora_inicio,
                'endTime' => $h->hora_fim,
            ];
        });
    });

    return response()->json($dados);
}

}
