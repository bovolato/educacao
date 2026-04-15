<?php

namespace App\Models;

use App\Models\Pessoas\Pessoa;
use App\Models\Institucional\{Municipio, Escola};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'pessoa_id', 'municipio_id', 'escola_id', 'name', 'email',
        'username', 'password', 'ultimo_login_em', 'ultimo_ip', 'ativo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_login_em'   => 'datetime',
        'password'          => 'hashed',
        'ativo'             => 'boolean',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function isSecretariaMunicipal(): bool
    {
        return $this->hasRole(['super_admin', 'secretaria_municipal']);
    }

    public function isGestorEscolar(): bool
    {
        return $this->hasRole('gestor_escolar');
    }

    public function isProfessor(): bool
    {
        return $this->hasRole('professor');
    }

    public function isAluno(): bool
    {
        return $this->hasRole('aluno');
    }

    public function isResponsavel(): bool
    {
        return $this->hasRole('responsavel');
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function registrarLogin(string $ip): void
    {
        $this->update(['ultimo_login_em' => now(), 'ultimo_ip' => $ip]);
    }
}
