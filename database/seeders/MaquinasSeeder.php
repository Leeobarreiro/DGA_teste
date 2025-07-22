<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maquina;

class MaquinasSeeder extends Seeder
{
    public function run(): void
    {
        
        Maquina::create(['nome' => 'Máquina A']);
        Maquina::create(['nome' => 'Máquina B']);
        Maquina::create(['nome' => 'Máquina C']);
    }
}
