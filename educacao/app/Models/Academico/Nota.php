<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Aluno;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';

    protected $fillable = ['avaliacao_id', 'aluno_id', 'matricula_id', 'nota', 'falta_na_avaliacao', 'observacao'];

    protected $casts = [
        'nota'              => 'decimal:2',
        'falta_na_avaliacao' => 'boolean',
    ];

    public function avaliacao()
    {
        return $this->belongsTo(Avaliacao::class);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }
}
