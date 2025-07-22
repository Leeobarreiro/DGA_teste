<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'maquina_id',
        'inicio',
        'fim',
        'cor',
    ];

    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }
}
