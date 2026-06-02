<?php

namespace App\Models\Institucional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';

    protected $fillable = [
        'nome', 'uf', 'codigo_ibge', 'cnpj', 'telefone', 'email',
        'logradouro', 'numero', 'bairro', 'cidade', 'cep', 'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function escolas()
    {
        return $this->hasMany(Escola::class);
    }

    public function anosLetivos()
    {
        return $this->hasMany(AnoLetivo::class);
    }

    public function etapasEnsino()
    {
        return $this->hasMany(EtapaEnsino::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }

    public function disciplinas()
    {
        return $this->hasMany(\App\Models\Academico\Disciplina::class);
    }
}
