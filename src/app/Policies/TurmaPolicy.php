<?php

namespace App\Policies;

use App\Models\Academico\Turma;
use App\Models\User;
use App\Services\EscopoAcesso;

class TurmaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Turma $turma): bool
    {
        return app(EscopoAcesso::class)->turmaAcessivelPeloUsuario($user, $turma);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Turma $turma): bool
    {
        return $this->view($user, $turma);
    }

    public function delete(User $user, Turma $turma): bool
    {
        return $this->view($user, $turma);
    }
}
