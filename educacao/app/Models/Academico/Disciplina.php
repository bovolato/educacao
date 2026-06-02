<?php

namespace App\Models\Academico;

use App\Models\Institucional\Municipio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;

    protected $table = 'disciplinas';

    protected $fillable = ['municipio_id', 'nome', 'sigla', 'descricao', 'carga_horaria', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function turmas()
    {
        return $this->belongsToMany(Turma::class, 'turma_disciplinas')->withTimestamps();
    }

    public function professores()
    {
        return $this->belongsToMany(\App\Models\Pessoas\Professor::class, 'turma_professores')
            ->withPivot(['turma_id', 'titular'])
            ->withTimestamps();
    }
}
