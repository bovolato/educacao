<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $table = 'aulas';

    protected $fillable = [
        'turma_id', 'disciplina_id', 'professor_id', 'data_aula',
        'hora_inicio', 'hora_fim', 'status',
    ];

    protected $casts = ['data_aula' => 'date'];

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function conteudos()
    {
        return $this->hasMany(ConteudoAula::class);
    }

    public function frequencias()
    {
        return $this->hasMany(Frequencia::class);
    }
}
