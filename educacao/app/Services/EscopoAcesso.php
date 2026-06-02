<?php

namespace App\Services;

use App\Models\Academico\{Aula, Matricula, Turma};
use App\Models\Institucional\Escola;
use App\Models\Pessoas\{Aluno, Professor};
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EscopoAcesso
{
    /** gestor_escolar ou secretario_escolar: escola fixa; demais perfis: null (visão ampla). */
    public function escolaIdObrigatorioParaUsuarioEscola(User $user): ?int
    {
        if ($user->hasRole(['gestor_escolar', 'secretario_escolar'])) {
            return $user->escola_id ? (int) $user->escola_id : null;
        }

        return null;
    }

    public function escolaDoUsuario(User $user): ?Escola
    {
        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return null;
        }

        return Escola::find($eid);
    }

    public function professorLogado(User $user): ?Professor
    {
        return Professor::where('user_id', $user->id)->first();
    }

    public function usuarioENivelAmplo(User $user): bool
    {
        return $user->hasRole(['super_admin', 'secretaria_municipal', 'coordenador']);
    }

    public function aplicarEscopoAlunos(Builder $query, User $user): Builder
    {
        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return $query;
        }

        $escola      = Escola::find($eid);
        $municipioId = $escola?->municipio_id;

        return $query->where(function (Builder $q) use ($eid, $municipioId) {
            $q->whereHas('matriculas', fn (Builder $m) => $m->where('escola_id', $eid));
            if ($municipioId) {
                $q->orWhere('municipio_id', $municipioId);
            }
        });
    }

    public function alunoAcessivelPeloUsuario(User $user, Aluno $aluno): bool
    {
        if ($this->usuarioENivelAmplo($user)) {
            return true;
        }

        if ($user->hasRole('professor')) {
            $prof = $this->professorLogado($user);
            if (! $prof) {
                return false;
            }
            $turmaIds = $prof->turmas()->pluck('turmas.id');

            return $aluno->matriculas()->where('situacao', 'ativa')->whereIn('turma_id', $turmaIds)->exists();
        }

        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return false;
        }

        $escola = Escola::find($eid);
        if ($aluno->matriculas()->where('escola_id', $eid)->exists()) {
            return true;
        }

        return (bool) ($escola?->municipio_id && (int) $aluno->municipio_id === (int) $escola->municipio_id);
    }

    public function aplicarEscopoProfessores(Builder $query, User $user): Builder
    {
        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return $query;
        }

        return $query->where('escola_id', $eid);
    }

    public function professorAcessivelPeloUsuario(User $user, Professor $professor): bool
    {
        if ($this->usuarioENivelAmplo($user)) {
            return true;
        }

        if ($user->hasRole('professor')) {
            $eu = $this->professorLogado($user);

            return $eu && (int) $eu->id === (int) $professor->id;
        }

        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return false;
        }

        return (int) $professor->escola_id === $eid;
    }

    public function aplicarEscopoTurmas(Builder $query, User $user): Builder
    {
        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return $query;
        }

        return $query->where('escola_id', $eid);
    }

    public function turmaAcessivelPeloUsuario(User $user, Turma $turma): bool
    {
        if ($this->usuarioENivelAmplo($user)) {
            return true;
        }

        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid !== null) {
            return (int) $turma->escola_id === $eid;
        }

        if ($user->hasRole('professor')) {
            $prof = $this->professorLogado($user);
            if (! $prof) {
                return false;
            }

            return $prof->turmas()->where('turmas.id', $turma->id)->exists();
        }

        return false;
    }

    public function aplicarEscopoMatriculas(Builder $query, User $user): Builder
    {
        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return $query;
        }

        return $query->where('escola_id', $eid);
    }

    public function matriculaAcessivelPeloUsuario(User $user, Matricula $matricula): bool
    {
        if ($this->usuarioENivelAmplo($user)) {
            return true;
        }

        if ($user->hasRole('professor')) {
            $prof = $this->professorLogado($user);
            if (! $prof) {
                return false;
            }

            return $prof->turmas()->where('turmas.id', $matricula->turma_id)->exists();
        }

        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return false;
        }

        return (int) $matricula->escola_id === $eid;
    }

    /** Valida turma + escola coerentes com matrícula (gestor não troca escola). */
    public function matriculaPayloadCoerenteComEscolaDoUsuario(User $user, int $escolaId, int $turmaId): bool
    {
        $eid = $this->escolaIdObrigatorioParaUsuarioEscola($user);
        if ($eid === null) {
            return true;
        }

        if ((int) $escolaId !== $eid) {
            return false;
        }

        $turma = Turma::find($turmaId);

        return $turma && (int) $turma->escola_id === $eid;
    }

    public function professorLecaDisciplinaNaTurma(Professor $professor, int $turmaId, int $disciplinaId): bool
    {
        return DB::table('turma_professores')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $turmaId)
            ->where('disciplina_id', $disciplinaId)
            ->exists();
    }

    /** Para turmas polivalentes: valida se o professor tem qualquer vínculo com a turma (independente da disciplina). */
    public function professorAcessaTurma(Professor $professor, int $turmaId): bool
    {
        return DB::table('turma_professores')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $turmaId)
            ->exists();
    }

    public function aulaPertenceAoProfessor(Professor $professor, Aula $aula): bool
    {
        if ((int) $aula->professor_id !== (int) $professor->id) {
            return false;
        }

        $turma = Turma::find($aula->turma_id);
        if ($turma?->polivalente) {
            return $this->professorAcessaTurma($professor, (int) $aula->turma_id);
        }

        return $this->professorLecaDisciplinaNaTurma($professor, (int) $aula->turma_id, (int) $aula->disciplina_id);
    }
}
