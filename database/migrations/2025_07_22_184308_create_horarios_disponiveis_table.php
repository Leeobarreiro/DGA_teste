<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_disponiveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('dia_semana', ['domingo','segunda','terça','quarta','quinta','sexta','sábado']);
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_disponiveis');
    }
};
