<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanoAula extends Model
{
    protected $table = 'planos_aula';

    protected $fillable = [
        'professor_id', 'turma_id', 'disciplina_id',
        'data_prevista', 'titulo', 'objetivos', 'conteudo_previsto', 'recursos',
        'periodo',
    ];

    protected $casts = ['data_prevista' => 'date'];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }
}
