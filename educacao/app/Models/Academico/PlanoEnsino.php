<?php

namespace App\Models\Academico;

use App\Models\Institucional\AnoLetivo;
use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanoEnsino extends Model
{
    protected $table = 'planos_ensino';

    protected $fillable = [
        'professor_id', 'turma_id', 'disciplina_id', 'ano_letivo_id',
        'titulo', 'objetivos', 'metodologia', 'criterios_avaliacao',
        'periodo',
    ];

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

    public function anoLetivo(): BelongsTo
    {
        return $this->belongsTo(AnoLetivo::class);
    }
}
