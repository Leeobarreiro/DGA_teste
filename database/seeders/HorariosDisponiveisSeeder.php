<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maquina;
use App\Models\HorarioDisponivel;

class HorariosDisponiveisSeeder extends Seeder
{
    public function run(): void
    {
        $dias = ['segunda', 'terça', 'quarta', 'quinta', 'sexta'];

        foreach (Maquina::all() as $maquina) {
            foreach ($dias as $dia) {
                HorarioDisponivel::create([
                    'maquina_id' => $maquina->id,
                    'dia_semana' => $dia,
                    'hora_inicio' => '08:00:00',
                    'hora_fim' => '18:00:00',
                ]);
            }
        }
    }
}
