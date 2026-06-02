<?php

namespace App\Models\Comunicacao;

use App\Models\Institucional\{Municipio, Escola};
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    protected $table = 'avisos';

    protected $fillable = [
        'municipio_id', 'escola_id', 'usuario_id', 'titulo', 'mensagem',
        'tipo_destino', 'publicado_em', 'ativo',
    ];

    protected $casts = [
        'publicado_em' => 'datetime',
        'ativo'        => 'boolean',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function destinatarios()
    {
        return $this->hasMany(AvisoDestinatario::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
