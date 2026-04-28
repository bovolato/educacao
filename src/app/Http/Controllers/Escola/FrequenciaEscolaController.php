<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Aula, Frequencia, Turma};
use App\Services\EscopoAcesso;
class FrequenciaEscolaController extends Controller
{
    private function escolaIdObrigatorio(): int
    {
        $eid = app(EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola(auth()->user());
        abort_if($eid === null, 403);

        return $eid;
    }

    public function index()
    {
        $eid = $this->escolaIdObrigatorio();

        $turmas = Turma::query()
            ->where('escola_id', $eid)
            ->where('status', 'ativa')
            ->with(['serie', 'turno'])
            ->withCount([
                'aulas as aulas_realizadas_count' => fn ($q) => $q->where('status', 'realizada'),
            ])
            ->orderBy('nome')
            ->get();

        return view('escola.frequencias.index', compact('turmas'));
    }

    public function turma(Turma $turma)
    {
        $this->authorize('view', $turma);
        $eid = $this->escolaIdObrigatorio();
        abort_if((int) $turma->escola_id !== $eid, 403);

        $aulas = Aula::query()
            ->where('turma_id', $turma->id)
            ->with(['disciplina', 'professor.pessoa'])
            ->withCount([
                'frequencias as faltas_count' => fn ($q) => $q->where('situacao', 'falta'),
                'frequencias as presencas_count' => fn ($q) => $q->where('situacao', 'presente'),
            ])
            ->orderByDesc('data_aula')
            ->paginate(15)
            ->withQueryString();

        return view('escola.frequencias.turma', compact('turma', 'aulas'));
    }

    public function aula(Aula $aula)
    {
        $eid = $this->escolaIdObrigatorio();
        $aula->load('turma');
        abort_if((int) $aula->turma->escola_id !== $eid, 403);

        $matriculas = $aula->turma->matriculasAtivas()
            ->with(['aluno.pessoa'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('matriculas.*')
            ->get();

        $freqPorMatricula = Frequencia::query()
            ->where('aula_id', $aula->id)
            ->get()
            ->keyBy('matricula_id');

        return view('escola.frequencias.aula', compact('aula', 'matriculas', 'freqPorMatricula'));
    }
}
