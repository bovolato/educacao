<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Aluno;
use App\Models\Institucional\{Escola, AnoLetivo};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    protected $fillable = [
        'aluno_id', 'escola_id', 'ano_letivo_id', 'turma_id', 'numero_matricula',
        'data_matricula', 'situacao', 'origem', 'observacoes', 'criado_por',
    ];

    protected $casts = [
        'data_matricula' => 'date',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function anoLetivo()
    {
        return $this->belongsTo(AnoLetivo::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function historicos()
    {
        return $this->hasMany(HistoricoMatricula::class);
    }

    public function frequencias()
    {
        return $this->hasMany(Frequencia::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function boletins()
    {
        return $this->hasMany(Boletim::class);
    }

    public function isAtiva(): bool
    {
        return $this->situacao === 'ativa';
    }

    public function scopeAtiva($query)
    {
        return $query->where('situacao', 'ativa');
    }
}
