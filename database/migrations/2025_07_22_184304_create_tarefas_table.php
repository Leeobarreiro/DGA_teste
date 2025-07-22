<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->foreignId('maquina_id')->nullable()->constrained('maquinas')->nullOnDelete()->cascadeOnUpdate();
            $table->dateTime('inicio');
            $table->dateTime('fim');
            $table->string('cor', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
