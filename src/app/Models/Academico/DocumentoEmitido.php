<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Aluno;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoEmitido extends Model
{
    protected $table = 'documentos_emitidos';

    protected $fillable = [
        'aluno_id', 'matricula_id', 'tipo_documento', 'arquivo',
        'emitido_por_usuario_id', 'emitido_em',
    ];

    protected $casts = ['emitido_em' => 'datetime'];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function emitidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitido_por_usuario_id');
    }
}
