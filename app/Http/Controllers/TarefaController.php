<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;
use App\Models\HorarioDisponivel;
use Illuminate\Container\Attributes\Log;

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
    date_default_timezone_set('America/Sao_Paulo');

    $tarefa = Tarefa::findOrFail($request->id);
    $maquinaId = $request->maquina_id;
    $inicio = \Carbon\Carbon::parse($request->inicio);
    $fim = \Carbon\Carbon::parse($request->fim);

    // Traduzir dia da semana do inglês para português
    $mapaDias = [
        'monday'    => 'segunda',
        'tuesday'   => 'terca',
        'wednesday' => 'quarta',
        'thursday'  => 'quinta',
        'friday'    => 'sexta',
        'saturday'  => 'sabado',
        'sunday'    => 'domingo',
    ];

    $diaIngles = strtolower($inicio->format('l'));
    $diaSemana = $mapaDias[$diaIngles] ?? $diaIngles;

    // Buscar todos os horários permitidos para essa máquina e dia
    $horariosPermitidos = HorarioDisponivel::where('maquina_id', $maquinaId)
        ->where('dia_semana', $diaSemana)
        ->get();

    if ($horariosPermitidos->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Não há horário disponível para esse dia.']);
    }

    // Verifica se o novo horário da tarefa está dentro de algum dos horários disponíveis
    $valido = $horariosPermitidos->contains(function ($h) use ($inicio, $fim) {
        return $inicio->format('H:i:s') >= $h->hora_inicio &&
               $fim->format('H:i:s') <= $h->hora_fim;
    });

    if (!$valido) {
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

        public function criar(Request $request)
        {
            date_default_timezone_set('America/Sao_Paulo');

            $request->validate([
                'titulo' => 'required|string|max:255',
                'maquina_id' => 'required|exists:maquinas,id',
                'inicio' => 'required|date',
                'fim' => 'required|date|after:inicio',
                'cor' => 'required|string',
            ]);

            $inicio = \Carbon\Carbon::parse($request->inicio);
            $fim = \Carbon\Carbon::parse($request->fim);
            $dias = [
                'Monday'    => 'segunda',
                'Tuesday'   => 'terça',
                'Wednesday' => 'quarta',
                'Thursday'  => 'quinta',
                'Friday'    => 'sexta',
                'Saturday'  => 'sábado',
                'Sunday'    => 'domingo',
            ];

            $diaSemanaIngles = $inicio->format('l'); 
            $diaSemana = $dias[$diaSemanaIngles];   

            $horarios = HorarioDisponivel::where('maquina_id', $request->maquina_id)
                ->where('dia_semana', $diaSemana)
                ->get();

            $valido = $horarios->contains(function ($h) use ($inicio, $fim) {
            $inicioHorario = \Carbon\Carbon::createFromFormat('H:i:s', $inicio->format('H:i:s'));
            $fimHorario = \Carbon\Carbon::createFromFormat('H:i:s', $fim->format('H:i:s'));

            $inicioPermitido = \Carbon\Carbon::createFromFormat('H:i:s', $h->hora_inicio);
            $fimPermitido = \Carbon\Carbon::createFromFormat('H:i:s', $h->hora_fim);

            return $inicioHorario->greaterThanOrEqualTo($inicioPermitido)
                && $fimHorario->lessThanOrEqualTo($fimPermitido);
        });


            if (!$valido) {
                return response()->json(['success' => false, 'message' => 'O horário está fora da faixa permitida para essa máquina.']);
            }

            Tarefa::create([
                'titulo' => $request->titulo,
                'maquina_id' => $request->maquina_id,
                'inicio' => $inicio,
                'fim' => $fim,
                'cor' => $request->cor,
            ]);

            return response()->json(['success' => true]);
        }

        public function deletar($id)
    {
        $tarefa = Tarefa::find($id);

        if (!$tarefa) {
            return response()->json(['success' => false, 'message' => 'Tarefa não encontrada.']);
        }

        $tarefa->delete();

        return response()->json(['success' => true, 'message' => 'Tarefa excluída com sucesso.']);
    }

}
