<?php

namespace App\Models\Pessoas;

use App\Models\Academico\Matricula;
use App\Models\Academico\Frequencia;
use App\Models\Academico\Nota;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $table = 'alunos';

    protected $fillable = [
        'pessoa_id', 'cidade_vinculo', 'ra', 'codigo_aluno', 'nis', 'sus',
        'necessidades_especiais', 'descricao_necessidades',
        'observacoes_saude', 'usa_transporte', 'ativo',
    ];

    protected $casts = [
        'necessidades_especiais' => 'boolean',
        'usa_transporte'         => 'boolean',
        'ativo'                  => 'boolean',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
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
        if ($this->pessoa) {
            return $this->pessoa->nome_exibicao;
        }

        return 'Aluno #'.$this->id;
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
