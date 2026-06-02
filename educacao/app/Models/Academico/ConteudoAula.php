<?php

namespace App\Models\Academico;

use App\Models\Pessoas\Professor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConteudoAula extends Model
{
    use HasFactory;

    protected $table = 'conteudos_aula';

    protected $fillable = [
        'aula_id', 'professor_id', 'titulo', 'descricao', 'material_utilizado', 'tarefa_passada',
    ];

    protected $casts = ['tarefa_passada' => 'boolean'];

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
