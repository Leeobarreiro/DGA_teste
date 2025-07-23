<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\HorarioController;



Route::get('/', function () {
    return view('welcome');
});

// Organizando Tarefas
Route::prefix('tarefas')->group(function () {
    Route::get('/', [TarefaController::class, 'index']);
    Route::get('/json', [TarefaController::class, 'json']);
    Route::post('/atualizar', [TarefaController::class, 'atualizar']);
    Route::post('/criar', [TarefaController::class, 'criar']);
    Route::delete('/{id}', [\App\Http\Controllers\TarefaController::class, 'deletar'])->name('tarefas.deletar');



});

Route::get('horarios/maquinas', [HorarioController::class, 'horariosPorMaquina']);


