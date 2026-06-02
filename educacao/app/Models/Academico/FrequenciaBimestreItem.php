<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequenciaBimestreItem extends Model
{
    use HasFactory;

    protected $table = 'frequencias_bimestre_itens';

    protected $fillable = [
        'frequencia_bimestre_id',
        'matricula_id',
        'aluno_id',
        'presencas',
        'faltas',
        'faltas_justificadas',
        'atrasos',
        'observacao',
    ];

    public function frequenciaBimestre()
    {
        return $this->belongsTo(FrequenciaBimestre::class, 'frequencia_bimestre_id');
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

