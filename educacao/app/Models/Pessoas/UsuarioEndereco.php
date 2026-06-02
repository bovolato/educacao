<?php

namespace App\Models\Pessoas;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioEndereco extends Model
{
    use HasFactory;

    protected $table = 'usuario_enderecos';

    protected $fillable = [
        'user_id', 'logradouro', 'numero', 'complemento',
        'bairro', 'cidade', 'uf', 'cep', 'principal',
    ];

    protected $casts = ['principal' => 'boolean'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
