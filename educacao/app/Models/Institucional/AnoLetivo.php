<?php

namespace App\Models\Institucional;

use App\Models\Academico\Turma;
use App\Models\Academico\Matricula;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnoLetivo extends Model
{
    use HasFactory;

    protected $table = 'anos_letivos';

    protected $fillable = [
        'municipio_id', 'descricao', 'data_inicio', 'data_fim', 'ativo', 'encerrado',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
        'ativo'       => 'boolean',
        'encerrado'   => 'boolean',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    public function calendarios()
    {
        return $this->hasMany(CalendarioLetivo::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
