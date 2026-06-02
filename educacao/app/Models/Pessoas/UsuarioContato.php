<?php

namespace App\Models\Pessoas;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioContato extends Model
{
    use HasFactory;

    protected $table = 'usuario_contatos';

    protected $fillable = ['user_id', 'tipo', 'valor', 'principal'];

    protected $casts = ['principal' => 'boolean'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
