<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academico\TurmaRequest;
use App\Models\Academico\{Turma, Disciplina, Matricula};
use App\Models\Institucional\{Escola, AnoLetivo, Serie, Turno, Sala};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $turmas = Turma::with(['escola', 'serie', 'turno', 'anoLetivo'])
            ->when($escopo->escolaIdObrigatorioParaUsuarioEscola($user) !== null, fn ($q) => $escopo->aplicarEscopoTurmas($q, $user))
            ->withCount('matriculasAtivas')
            ->when($request->filled('busca'), fn($q) => $q->where('nome', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('serie'), fn($q) => $q->where('serie_id', $request->serie))
            ->when($request->filled('escola'), fn($q) => $q->where('escola_id', $request->escola))
            ->when(
                $request->filled('cidade') && ! $request->filled('escola'),
                fn($q) => $q->whereHas('escola', fn($e) => $e->where('cidade', $request->cidade))
            )
            ->orderBy('nome')
            ->paginate(10)->withQueryString();

        $series = Serie::where('ativo', true)->orderBy('nome')->get();

        $cidades = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $escolas = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->when($request->filled('cidade'), fn($q) => $q->where('cidade', $request->cidade))
            ->orderBy('nome')
            ->get(['id', 'nome', 'cidade']);

        return view('academico.turmas.index', compact('turmas', 'series', 'cidades', 'escolas'));
    }

    public function create(Request $request)
    {
        $escopo     = app(EscopoAcesso::class);
        $escolaFixa = $escopo->escolaDoUsuario($request->user());

        [$cidades, $escolas] = $this->cidadesEEscolasParaFormularioTurma($escolaFixa);

        $anos   = AnoLetivo::where('ativo', true)->orderBy('descricao')->get();
        $series = Serie::where('ativo', true)->orderBy('nome')->get();
        $turnos = Turno::where('ativo', true)->orderBy('nome')->get();
        $salas  = $escolaFixa
            ? Sala::where('escola_id', $escolaFixa->id)->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('academico.turmas.create', compact('cidades', 'escolas', 'anos', 'series', 'turnos', 'salas', 'escolaFixa'));
    }

    public function store(TurmaRequest $request)
    {
        $data = $request->validated();
        if ($eid = app(EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola($request->user())) {
            $data['escola_id'] = $eid;
        }
        $data['polivalente'] = $request->boolean('polivalente');
        Turma::create($data);
        return redirect()->route('academico.turmas.index')->with('success', 'Turma criada com sucesso!');
    }

    public function show(Turma $turma)
    {
        $this->authorize('view', $turma);

        $turma->load(['escola', 'serie', 'turno', 'anoLetivo', 'sala']);

        $matriculasTurma = Matricula::query()
            ->where('turma_id', $turma->id)
            ->where('situacao', 'ativa')
            ->with(['aluno.pessoa'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('matriculas.*')
            ->paginate(10, ['*'], 'alunos_page')
            ->withQueryString();

        $professoresTurma = $turma->professores()
            ->with('pessoa')
            ->paginate(10, ['*'], 'professores_page')
            ->withQueryString();

        $disciplinasPorId = Disciplina::query()
            ->whereIn(
                'id',
                $professoresTurma->getCollection()->pluck('pivot.disciplina_id')->unique()->filter()->values()
            )->get()->keyBy('id');

        return view('academico.turmas.show', compact(
            'turma',
            'matriculasTurma',
            'professoresTurma',
            'disciplinasPorId'
        ));
    }

    public function edit(Turma $turma)
    {
        $this->authorize('update', $turma);

        $turma->loadMissing('escola');

        $escolaFixa = app(EscopoAcesso::class)->escolaDoUsuario(request()->user());

        [$cidades, $escolas] = $this->cidadesEEscolasParaFormularioTurma($escolaFixa);

        $anos   = AnoLetivo::where('ativo', true)->orderBy('descricao')->get();
        $series = Serie::where('ativo', true)->orderBy('nome')->get();
        $turnos = Turno::where('ativo', true)->orderBy('nome')->get();
        // carrega apenas as salas da escola desta turma
        $salas = $turma->escola_id
            ? Sala::where('escola_id', $turma->escola_id)->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('academico.turmas.edit', compact('turma', 'cidades', 'escolas', 'anos', 'series', 'turnos', 'salas', 'escolaFixa'));
    }

    public function update(TurmaRequest $request, Turma $turma)
    {
        $this->authorize('update', $turma);

        $data = $request->validated();
        if ($eid = app(EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola($request->user())) {
            $data['escola_id'] = $eid;
        }
        $data['polivalente'] = $request->boolean('polivalente');
        $turma->update($data);
        return redirect()->route('academico.turmas.index')->with('success', 'Turma atualizada!');
    }

    public function destroy(Turma $turma)
    {
        $this->authorize('delete', $turma);

        if ($turma->matriculas()->where('situacao', 'ativa')->exists()) {
            return back()->with('error', 'Não é possível excluir uma turma com matrículas ativas.');
        }
        $turma->update(['status' => 'encerrada']);
        return redirect()->route('academico.turmas.index')->with('success', 'Turma encerrada.');
    }

    /**
     * Cidades distintas e escolas ativas (com cidade) para filtros em formulários de turma.
     *
     * @return array{0: \Illuminate\Support\Collection<int, string>, 1: \Illuminate\Database\Eloquent\Collection<int, Escola>}
     */
    private function cidadesEEscolasParaFormularioTurma(?Escola $escolaFixa = null): array
    {
        $cidades = Escola::query()
            ->when($escolaFixa, fn ($q) => $q->where('id', $escolaFixa->id))
            ->where('status', 'ativa')
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $escolas = Escola::query()
            ->when($escolaFixa, fn ($q) => $q->where('id', $escolaFixa->id))
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome', 'cidade']);

        return [$cidades, $escolas];
    }
}
