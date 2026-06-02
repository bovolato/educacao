<?php

namespace App\Models\Pessoas;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsavel extends Model
{
    use HasFactory;

    protected $table = 'responsaveis';

    protected $fillable = [
        'user_id', 'tipo_responsavel', 'responsavel_legal', 'financeiro', 'recebe_notificacao',
    ];

    protected $casts = [
        'responsavel_legal'   => 'boolean',
        'financeiro'          => 'boolean',
        'recebe_notificacao'  => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alunos()
    {
        return $this->belongsToMany(Aluno::class, 'responsavel_aluno')
            ->withPivot(['grau_parentesco', 'responsavel_principal', 'retira_aluno', 'recebe_boletim'])
            ->withTimestamps();
    }

    public function getNomeAttribute(): string
    {
        if ($this->usuario) {
            return $this->usuario->nome_exibicao;
        }

        return 'Responsável #'.$this->id;
    }
}
