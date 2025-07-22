<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioDisponivel extends Model
{
    use HasFactory;

     // Nome correto da tabela para evitar erro
    protected $table = 'horarios_disponiveis';


    protected $fillable = [
        'maquina_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
    ];

    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }
}
