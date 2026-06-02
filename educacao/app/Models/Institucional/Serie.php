<?php

namespace App\Models\Institucional;

use App\Models\Academico\Turma;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = ['etapa_ensino_id', 'nome', 'sigla', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function etapaEnsino()
    {
        return $this->belongsTo(EtapaEnsino::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }
}
