<?php

namespace App\Models\Pessoas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PessoaEndereco extends Model
{
    use HasFactory;

    protected $table = 'pessoa_enderecos';

    protected $fillable = [
        'pessoa_id', 'logradouro', 'numero', 'complemento',
        'bairro', 'cidade', 'uf', 'cep', 'principal',
    ];

    protected $casts = ['principal' => 'boolean'];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
