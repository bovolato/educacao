<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnotacaoProfessor extends Model
{
    use HasFactory;

    protected $table = 'anotacoes_professor';

    protected $fillable = [
        'professor_id',
        'turma_id',
        'matricula_id',
        'aluno_id',
        'periodo',
        'assunto',
        'texto',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function aluno()
    {
        return $this->belongsTo(\App\Models\Pessoas\Aluno::class);
    }
}

