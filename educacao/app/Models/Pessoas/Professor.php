<?php

namespace App\Models\Pessoas;

use App\Models\Academico\Turma;
use App\Models\Academico\Aula;
use App\Models\Institucional\Escola;
use App\Models\Institucional\Municipio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professores';

    protected $fillable = [
        'user_id', 'escola_id', 'municipio_id', 'matricula_funcional', 'formacao', 'registro_profissional', 'data_admissao', 'ativo',
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'ativo'         => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
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
        if ($this->usuario) {
            return $this->usuario->nome_exibicao;
        }

        return 'Professor #'.$this->id;
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
