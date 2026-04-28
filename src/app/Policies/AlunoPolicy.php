<?php

namespace App\Policies;

use App\Models\Pessoas\Aluno;
use App\Models\User;
use App\Services\EscopoAcesso;

class AlunoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Aluno $aluno): bool
    {
        return app(EscopoAcesso::class)->alunoAcessivelPeloUsuario($user, $aluno);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Aluno $aluno): bool
    {
        return $this->view($user, $aluno);
    }

    public function delete(User $user, Aluno $aluno): bool
    {
        return $this->view($user, $aluno);
    }
}
