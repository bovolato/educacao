<?php

namespace App\Models\Institucional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioLetivo extends Model
{
    use HasFactory;

    protected $table = 'calendarios_letivos';

    protected $fillable = [
        'municipio_id', 'escola_id', 'ano_letivo_id', 'descricao',
        'data_inicio', 'data_fim', 'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function anoLetivo()
    {
        return $this->belongsTo(AnoLetivo::class);
    }
}
