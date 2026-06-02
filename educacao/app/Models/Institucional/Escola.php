<?php

namespace App\Models\Institucional;

use App\Models\Academico\Turma;
use App\Models\Academico\Matricula;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;

    protected $table = 'escolas';

    protected $fillable = [
        'municipio_id', 'nome', 'codigo', 'inep', 'cnpj', 'telefone', 'email',
        'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf', 'cep',
        'diretor_nome', 'status',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function salas()
    {
        return $this->hasMany(Sala::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    public function funcionarios()
    {
        return $this->hasMany(\App\Models\Pessoas\Funcionario::class);
    }

    public function professores()
    {
        return $this->hasMany(\App\Models\Pessoas\Professor::class);
    }

    public function isAtiva(): bool
    {
        return $this->status === 'ativa';
    }
}
