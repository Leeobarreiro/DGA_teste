<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarefa;
use App\Models\Maquina;
use Carbon\Carbon;

class TarefasSeeder extends Seeder
{
    public function run(): void
    {
        
        $maquinas = Maquina::all();
        $cores = ['#ff6384', '#36a2eb', '#cc65fe'];

        foreach ($maquinas as $index => $maquina) {
            Tarefa::create([
                'titulo' => 'Tarefa de teste ' . ($index + 1),
                'descricao' => 'Execução automática de produção.',
                'maquina_id' => $maquina->id,
                'inicio' => Carbon::now()->addDays($index)->setTime(9, 0),
                'fim' => Carbon::now()->addDays($index)->setTime(11, 0),
                'cor' => $cores[$index % count($cores)],
            ]);
        }
    }
}
