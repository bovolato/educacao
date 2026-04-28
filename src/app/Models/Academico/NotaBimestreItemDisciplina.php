<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaBimestreItemDisciplina extends Model
{
    use HasFactory;

    protected $table = 'notas_bimestre_itens_disciplinas';

    protected $fillable = [
        'nota_bimestre_item_id',
        'disciplina_id',
        'nota',
    ];

    public function item()
    {
        return $this->belongsTo(NotaBimestreItem::class, 'nota_bimestre_item_id');
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }
}

