<?php

namespace App\Models;

use App\Models\Pessoas\{Aluno, Professor, Responsavel, Funcionario, UsuarioContato, UsuarioEndereco};
use App\Models\Institucional\{Municipio, Escola};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'tipo', 'municipio_id', 'escola_id',
        'nome', 'nome_social', 'cpf', 'rg', 'rg_orgao_emissor', 'data_nascimento',
        'sexo', 'estado_civil', 'nome_mae', 'nome_pai', 'naturalidade',
        'naturalidade_uf', 'nacionalidade', 'foto', 'observacoes',
        'email', 'username', 'password', 'ultimo_login_em', 'ultimo_ip', 'ativo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_login_em'   => 'datetime',
        'data_nascimento'   => 'date',
        'password'          => 'hashed',
        'ativo'             => 'boolean',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function aluno()
    {
        return $this->hasOne(Aluno::class);
    }

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    public function responsavel()
    {
        return $this->hasOne(Responsavel::class);
    }

    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }

    public function contatos()
    {
        return $this->hasMany(UsuarioContato::class);
    }

    public function enderecos()
    {
        return $this->hasMany(UsuarioEndereco::class);
    }

    public function contatoPrincipal()
    {
        return $this->hasOne(UsuarioContato::class)->where('principal', true);
    }

    public function enderecoPrincipal()
    {
        return $this->hasOne(UsuarioEndereco::class)->where('principal', true);
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->nome_social ?? $this->nome;
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento?->age;
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
