<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academico\TurmaRequest;
use App\Models\Academico\{Turma, Disciplina};
use App\Models\Institucional\{Escola, AnoLetivo, Serie, Turno, Sala};
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index(Request $request)
    {
        $turmas = Turma::with(['escola', 'serie', 'turno', 'anoLetivo'])
            ->withCount('matriculasAtivas')
            ->when($request->filled('busca'), fn($q) => $q->where('nome', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('serie'), fn($q) => $q->where('serie_id', $request->serie))
            ->orderBy('nome')
            ->paginate(20)->withQueryString();

        $series = Serie::where('ativo', true)->orderBy('nome')->get();

        return view('academico.turmas.index', compact('turmas', 'series'));
    }

    public function create()
    {
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $anos    = AnoLetivo::where('ativo', true)->orderBy('descricao')->get();
        $series  = Serie::where('ativo', true)->orderBy('nome')->get();
        $turnos  = Turno::where('ativo', true)->orderBy('nome')->get();
        $salas   = collect(); // carregadas via AJAX pelo escola_id selecionado

        return view('academico.turmas.create', compact('escolas', 'anos', 'series', 'turnos', 'salas'));
    }

    public function store(TurmaRequest $request)
    {
        Turma::create($request->validated());
        return redirect()->route('academico.turmas.index')->with('success', 'Turma criada com sucesso!');
    }

    public function show(Turma $turma)
    {
        $turma->load([
            'escola', 'serie', 'turno', 'anoLetivo', 'sala',
            'disciplinas',
            'professores.pessoa',
            'matriculasAtivas.aluno.pessoa',
        ]);

        return view('academico.turmas.show', compact('turma'));
    }

    public function edit(Turma $turma)
    {
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $anos    = AnoLetivo::where('ativo', true)->orderBy('descricao')->get();
        $series  = Serie::where('ativo', true)->orderBy('nome')->get();
        $turnos  = Turno::where('ativo', true)->orderBy('nome')->get();
        // carrega apenas as salas da escola desta turma
        $salas   = $turma->escola_id
            ? Sala::where('escola_id', $turma->escola_id)->where('ativo', true)->orderBy('nome')->get()
            : collect();

        return view('academico.turmas.edit', compact('turma', 'escolas', 'anos', 'series', 'turnos', 'salas'));
    }

    public function update(TurmaRequest $request, Turma $turma)
    {
        $turma->update($request->validated());
        return redirect()->route('academico.turmas.index')->with('success', 'Turma atualizada!');
    }

    public function destroy(Turma $turma)
    {
        if ($turma->matriculas()->where('situacao', 'ativa')->exists()) {
            return back()->with('error', 'Não é possível excluir uma turma com matrículas ativas.');
        }
        $turma->update(['status' => 'encerrada']);
        return redirect()->route('academico.turmas.index')->with('success', 'Turma encerrada.');
    }
}
