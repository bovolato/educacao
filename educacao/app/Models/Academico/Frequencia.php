<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Aluno;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequencia extends Model
{
    use HasFactory;

    protected $table = 'frequencias';

    protected $fillable = ['matricula_id', 'aula_id', 'aluno_id', 'situacao', 'observacao'];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function isPresente(): bool
    {
        return $this->situacao === 'presente';
    }
}
