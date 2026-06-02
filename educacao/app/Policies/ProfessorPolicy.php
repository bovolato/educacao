<?php

namespace App\Policies;

use App\Models\Pessoas\Professor;
use App\Models\User;
use App\Services\EscopoAcesso;

class ProfessorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Professor $professor): bool
    {
        return app(EscopoAcesso::class)->professorAcessivelPeloUsuario($user, $professor);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Professor $professor): bool
    {
        return $this->view($user, $professor);
    }

    public function delete(User $user, Professor $professor): bool
    {
        return $this->view($user, $professor);
    }

    public function vincularTurmas(User $user, Professor $professor): bool
    {
        return $this->view($user, $professor);
    }
}
