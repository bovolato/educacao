<?php

namespace App\Policies;

use App\Models\Academico\Matricula;
use App\Models\User;
use App\Services\EscopoAcesso;

class MatriculaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Matricula $matricula): bool
    {
        return app(EscopoAcesso::class)->matriculaAcessivelPeloUsuario($user, $matricula);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Matricula $matricula): bool
    {
        return $this->view($user, $matricula);
    }

    public function delete(User $user, Matricula $matricula): bool
    {
        return $this->view($user, $matricula);
    }
}
