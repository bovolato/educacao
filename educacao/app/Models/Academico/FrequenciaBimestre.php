<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequenciaBimestre extends Model
{
    use HasFactory;

    protected $table = 'frequencias_bimestre';

    protected $fillable = [
        'professor_id',
        'turma_id',
        'disciplina_id',
        'periodo',
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function itens()
    {
        return $this->hasMany(FrequenciaBimestreItem::class, 'frequencia_bimestre_id');
    }
}

