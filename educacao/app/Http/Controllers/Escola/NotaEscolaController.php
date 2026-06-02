<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Avaliacao, Nota, Turma};
use App\Services\EscopoAcesso;

class NotaEscolaController extends Controller
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
            ->withCount('avaliacoes')
            ->orderBy('nome')
            ->get();

        return view('escola.notas.index', compact('turmas'));
    }

    public function turma(Turma $turma)
    {
        $this->authorize('view', $turma);
        $eid = $this->escolaIdObrigatorio();
        abort_if((int) $turma->escola_id !== $eid, 403);

        $avaliacoes = Avaliacao::query()
            ->where('turma_id', $turma->id)
            ->with(['disciplina', 'professor.usuario'])
            ->withCount('notas')
            ->orderByDesc('data_avaliacao')
            ->paginate(12)
            ->withQueryString();

        return view('escola.notas.turma', compact('turma', 'avaliacoes'));
    }

    public function avaliacao(Avaliacao $avaliacao)
    {
        $eid = $this->escolaIdObrigatorio();
        $avaliacao->load('turma');
        abort_if((int) $avaliacao->turma->escola_id !== $eid, 403);

        $notas = Nota::query()
            ->where('avaliacao_id', $avaliacao->id)
            ->with(['aluno.usuario', 'matricula'])
            ->join('alunos', 'notas.aluno_id', '=', 'alunos.id')
            ->join('users', 'alunos.user_id', '=', 'users.id')
            ->orderBy('users.nome')
            ->select('notas.*')
            ->get();

        return view('escola.notas.avaliacao', compact('avaliacao', 'notas'));
    }
}
