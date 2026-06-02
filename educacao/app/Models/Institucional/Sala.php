<?php

namespace App\Models\Institucional;

use App\Models\Academico\Turma;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'salas';

    protected $fillable = ['escola_id', 'nome', 'codigo', 'capacidade', 'tipo', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }
}
