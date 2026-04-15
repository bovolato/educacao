<?php

namespace App\Models\Academico;

use App\Models\Institucional\{Escola, AnoLetivo, Serie, Turno, Sala};
use App\Models\Pessoas\{Aluno, Professor};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $table = 'turmas';

    protected $fillable = [
        'escola_id', 'ano_letivo_id', 'serie_id', 'turno_id',
        'sala_id', 'nome', 'codigo', 'capacidade', 'status',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function anoLetivo()
    {
        return $this->belongsTo(AnoLetivo::class);
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }

    public function disciplinas()
    {
        return $this->belongsToMany(Disciplina::class, 'turma_disciplinas')
            ->withPivot(['carga_horaria'])
            ->withTimestamps();
    }

    public function professores()
    {
        return $this->belongsToMany(Professor::class, 'turma_professores')
            ->withPivot(['disciplina_id', 'titular'])
            ->withTimestamps();
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    public function matriculasAtivas()
    {
        return $this->hasMany(Matricula::class)->where('situacao', 'ativa');
    }

    public function aulas()
    {
        return $this->hasMany(Aula::class);
    }

    public function getTotalAlunosAttribute(): int
    {
        return $this->matriculasAtivas()->count();
    }

    public function isAtiva(): bool
    {
        return $this->status === 'ativa';
    }
}
