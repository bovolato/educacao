<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boletim extends Model
{
    use HasFactory;

    protected $table = 'boletins';

    protected $fillable = ['matricula_id', 'disciplina_id', 'periodo', 'media', 'faltas', 'situacao', 'fechado_em'];

    protected $casts = [
        'media'      => 'decimal:2',
        'fechado_em' => 'datetime',
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function isFechado(): bool
    {
        return $this->fechado_em !== null;
    }
}
