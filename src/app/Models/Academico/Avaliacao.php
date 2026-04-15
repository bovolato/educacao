<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'turma_id', 'disciplina_id', 'professor_id', 'titulo', 'tipo',
        'data_avaliacao', 'valor', 'periodo', 'descricao',
    ];

    protected $casts = ['data_avaliacao' => 'date', 'valor' => 'decimal:2'];

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

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
}
