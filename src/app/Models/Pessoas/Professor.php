<?php

namespace App\Models\Pessoas;

use App\Models\Academico\Turma;
use App\Models\Academico\Aula;
use App\Models\Institucional\Escola;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professores';

    protected $fillable = [
        'pessoa_id', 'escola_id', 'matricula_funcional', 'formacao', 'registro_profissional', 'data_admissao', 'ativo',
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'ativo'         => 'boolean',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function turmas()
    {
        return $this->belongsToMany(Turma::class, 'turma_professores')
            ->withPivot(['disciplina_id', 'titular'])
            ->withTimestamps();
    }

    public function aulas()
    {
        return $this->hasMany(Aula::class);
    }

    public function getNomeAttribute(): string
    {
        return $this->pessoa?->nome_exibicao ?? '';
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
