<?php

namespace App\Models\Pessoas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    use HasFactory;

    protected $table = 'pessoas';

    protected $fillable = [
        'nome', 'nome_social', 'cpf', 'rg', 'rg_orgao_emissor', 'data_nascimento',
        'sexo', 'estado_civil', 'nome_mae', 'nome_pai', 'naturalidade',
        'naturalidade_uf', 'nacionalidade', 'foto', 'observacoes', 'ativo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ativo'           => 'boolean',
    ];

    public function contatos()
    {
        return $this->hasMany(PessoaContato::class);
    }

    public function enderecos()
    {
        return $this->hasMany(PessoaEndereco::class);
    }

    public function enderecoPrincipal()
    {
        return $this->hasOne(PessoaEndereco::class)->where('principal', true);
    }

    public function contatoPrincipal()
    {
        return $this->hasOne(PessoaContato::class)->where('principal', true);
    }

    public function usuario()
    {
        return $this->hasOne(\App\Models\User::class);
    }

    public function aluno()
    {
        return $this->hasOne(Aluno::class);
    }

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    public function responsavel()
    {
        return $this->hasOne(Responsavel::class);
    }

    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->nome_social ?? $this->nome;
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento?->age;
    }
}
