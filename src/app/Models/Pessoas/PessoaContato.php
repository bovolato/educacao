<?php

namespace App\Models\Pessoas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PessoaContato extends Model
{
    use HasFactory;

    protected $table = 'pessoa_contatos';

    protected $fillable = ['pessoa_id', 'tipo', 'valor', 'principal'];

    protected $casts = ['principal' => 'boolean'];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
