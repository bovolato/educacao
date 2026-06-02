<?php

namespace App\Models\Comunicacao;

use App\Models\Academico\Turma;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AvisoDestinatario extends Model
{
    protected $table = 'aviso_destinatarios';

    protected $fillable = ['aviso_id', 'perfil_id', 'turma_id', 'usuario_id'];

    public function aviso()
    {
        return $this->belongsTo(Aviso::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
