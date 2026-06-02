<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    protected $fillable = [
        'turma_id', 'disciplina_id', 'professor_id',
        'titulo', 'descricao', 'data_postagem', 'data_entrega', 'valor',
        'periodo',
    ];

    protected $casts = [
        'data_postagem' => 'date',
        'data_entrega'  => 'date',
        'valor'         => 'decimal:2',
    ];

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }
}
