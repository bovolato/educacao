<?php

namespace App\Models\Pessoas;

use App\Models\Institucional\Escola;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $table = 'funcionarios';

    protected $fillable = [
        'pessoa_id', 'escola_id', 'matricula_funcional', 'cargo', 'setor', 'data_admissao', 'ativo',
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

    public function getNomeAttribute(): string
    {
        return $this->pessoa?->nome_exibicao ?? '';
    }
}
