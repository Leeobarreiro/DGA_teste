<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;
use App\Models\HorarioDisponivel;


class TarefaController extends Controller
{
    // View principal com o Gantt
    public function index()
    {
        return view('tarefas.index');
    }

    // Retorna JSON das tarefas para o FullCalendar
    public function json()
    {
        $tarefas = Tarefa::with('maquina')->get();

        $eventos = $tarefas->map(function ($tarefa) {
            return [
                'id' => $tarefa->id,
                'title' => $tarefa->titulo . ' (' . $tarefa->maquina->nome . ')',
                'start' => $tarefa->inicio,
                'end' => $tarefa->fim,
                'resourceId' => $tarefa->maquina_id,
                'color' => $tarefa->cor,
            ];
        });

        return response()->json($eventos);
    }

    public function atualizar(Request $request)
{
    $tarefa = Tarefa::findOrFail($request->id);
    $maquinaId = $request->maquina_id;
    $inicio = \Carbon\Carbon::parse($request->inicio);
    $fim = \Carbon\Carbon::parse($request->fim);
    setlocale(LC_TIME, 'pt_BR.UTF-8'); // força o locale para português do sistema
    $diaSemana = strtolower($inicio->translatedFormat('l')); // traduz corretamente o nome do dia


    // Buscar horário permitido para essa máquina e dia
    $horarioPermitido = HorarioDisponivel::where('maquina_id', $maquinaId)
        ->where('dia_semana', $diaSemana)
        ->first();

    if (!$horarioPermitido) {
        return response()->json(['success' => false, 'message' => 'Não há horário disponível para esse dia.']);
    }

    $inicioPermitido = \Carbon\Carbon::createFromTimeString($horarioPermitido->hora_inicio);
    $fimPermitido = \Carbon\Carbon::createFromTimeString($horarioPermitido->hora_fim);

    if (
        $inicio->format('H:i:s') < $inicioPermitido->format('H:i:s') ||
        $fim->format('H:i:s') > $fimPermitido->format('H:i:s')
    ) {
        return response()->json(['success' => false, 'message' => 'O novo horário não está dentro da faixa permitida da máquina.']);
    }

    // Atualiza a tarefa
    $tarefa->update([
        'maquina_id' => $maquinaId,
        'inicio' => $inicio,
        'fim' => $fim,
    ]);

    return response()->json(['success' => true]);
}
}
