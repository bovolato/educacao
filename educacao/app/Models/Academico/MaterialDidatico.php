<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialDidatico extends Model
{
    protected $table = 'materiais_didaticos';

    protected $fillable = [
        'professor_id', 'disciplina_id', 'turma_id',
        'titulo', 'descricao', 'arquivo', 'link', 'visivel_aluno',
        'periodo',
    ];

    protected $casts = ['visivel_aluno' => 'boolean'];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }
}
