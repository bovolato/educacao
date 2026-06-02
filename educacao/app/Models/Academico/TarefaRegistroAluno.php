<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarefaRegistroAluno extends Model
{
    use HasFactory;

    protected $table = 'tarefa_registros_alunos';

    protected $fillable = [
        'tarefa_id',
        'matricula_id',
        'professor_id',
        'status',
        'observacao',
    ];

    public function tarefa()
    {
        return $this->belongsTo(Tarefa::class);
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function professor()
    {
        return $this->belongsTo(\App\Models\Pessoas\Professor::class);
    }
}

