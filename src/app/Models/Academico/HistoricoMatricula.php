<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoMatricula extends Model
{
    use HasFactory;

    protected $table = 'historico_matriculas';

    protected $fillable = [
        'matricula_id', 'tipo_movimentacao', 'data_movimentacao', 'descricao', 'usuario_id',
    ];

    protected $casts = ['data_movimentacao' => 'date'];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
