<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarefaController;


Route::get('/', function () {
    return view('welcome');
});

// Organizando Tarefas
Route::prefix('tarefas')->group(function () {
    Route::get('/', [TarefaController::class, 'index']);
    Route::get('/json', [TarefaController::class, 'json']);
    Route::post('/atualizar', [TarefaController::class, 'atualizar']); 

});

