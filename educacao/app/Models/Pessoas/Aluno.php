<?php

namespace App\Models\Pessoas;

use App\Models\Academico\Matricula;
use App\Models\Academico\Frequencia;
use App\Models\Academico\Nota;
use App\Models\Institucional\Municipio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $table = 'alunos';

    protected $fillable = [
        'user_id', 'municipio_id', 'ra', 'codigo_aluno', 'nis', 'sus',
        'necessidades_especiais', 'descricao_necessidades',
        'observacoes_saude', 'usa_transporte', 'ativo',
    ];

    protected $casts = [
        'necessidades_especiais' => 'boolean',
        'usa_transporte'         => 'boolean',
        'ativo'                  => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    public function matriculaAtiva()
    {
        return $this->hasOne(Matricula::class)->where('situacao', 'ativa');
    }

    public function responsaveis()
    {
        return $this->belongsToMany(Responsavel::class, 'responsavel_aluno')
            ->withPivot(['grau_parentesco', 'responsavel_principal', 'retira_aluno', 'recebe_boletim'])
            ->withTimestamps();
    }

    public function responsavelPrincipal()
    {
        return $this->belongsToMany(Responsavel::class, 'responsavel_aluno')
            ->wherePivot('responsavel_principal', true)
            ->withPivot(['grau_parentesco', 'retira_aluno', 'recebe_boletim']);
    }

    public function frequencias()
    {
        return $this->hasMany(Frequencia::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function getNomeAttribute(): string
    {
        if ($this->usuario) {
            return $this->usuario->nome_exibicao;
        }

        return 'Aluno #'.$this->id;
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
