<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaBimestreItem extends Model
{
    use HasFactory;

    protected $table = 'notas_bimestre_itens';

    protected $fillable = [
        'nota_bimestre_id',
        'matricula_id',
        'aluno_id',
        'media_final',
        'observacao',
    ];

    public function notaBimestre()
    {
        return $this->belongsTo(NotaBimestre::class, 'nota_bimestre_id');
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function aluno()
    {
        return $this->belongsTo(\App\Models\Pessoas\Aluno::class);
    }

    public function disciplinas()
    {
        return $this->hasMany(NotaBimestreItemDisciplina::class, 'nota_bimestre_item_id');
    }
}

