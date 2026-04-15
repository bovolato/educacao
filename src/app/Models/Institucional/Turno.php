<?php

namespace App\Models\Institucional;

use App\Models\Academico\Turma;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turnos';

    protected $fillable = ['municipio_id', 'nome', 'hora_inicio', 'hora_fim', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }
}
