<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{AnotacaoProfessor, Matricula, Turma};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnotacoesProfessorController extends Controller
{
    private function vinculosProfessor($prof)
    {
        return DB::table('turma_professores as tp')
            ->where('tp.professor_id', $prof->id)
            ->join('turmas as t', 't.id', '=', 'tp.turma_id')
            ->leftJoin('escolas as e', 'e.id', '=', 't.escola_id')
            ->orderBy('t.nome')
            ->select([
                't.id as turma_id', 't.nome as turma_nome',
                't.polivalente as turma_polivalente',
                'e.nome as escola_nome',
            ])
            ->distinct()
            ->get();
    }

    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $vinculosTurmas = $this->vinculosProfessor($prof);

        $query = AnotacaoProfessor::query()
            ->where('professor_id', $prof->id)
            ->where('periodo', $periodoAtual)
            ->with(['turma', 'matricula.aluno.usuario'])
            ->when($request->filled('turma_id'), function ($q) use ($request, $escopo, $prof) {
                $tid = (int) $request->turma_id;
                abort_unless($escopo->professorAcessaTurma($prof, $tid), 403);
                return $q->where('turma_id', $tid);
            })
            ->when($request->filled('busca'), function ($q) use ($request) {
                $b = trim((string) $request->busca);
                return $q->where(function ($qq) use ($b) {
                    $qq->where('assunto', 'like', '%' . $b . '%')
                        ->orWhere('texto', 'like', '%' . $b . '%');
                });
            })
            ->orderByDesc('id');

        $anotacoes = $query->paginate(15)->withQueryString();

        $turmasOptions = $vinculosTurmas
            ->mapWithKeys(fn ($v) => [(int) $v->turma_id => $v->turma_nome . ($v->escola_nome ? ' · ' . $v->escola_nome : '')])
            ->toArray();

        return view('professor.anotacoes.index', compact('anotacoes', 'turmasOptions'));
    }

    public function create(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $request->validate([
            'turma_id' => 'nullable|exists:turmas,id',
            'matricula_id' => 'nullable|exists:matriculas,id',
        ]);

        $turmaId = $request->filled('turma_id') ? (int) $request->turma_id : null;
        $matriculaId = $request->filled('matricula_id') ? (int) $request->matricula_id : null;

        $vinculosTurmas = $this->vinculosProfessor($prof);
        abort_unless($vinculosTurmas->count() > 0, 403);

        $turmasOptions = $vinculosTurmas
            ->mapWithKeys(fn ($v) => [(int) $v->turma_id => $v->turma_nome . ($v->escola_nome ? ' · ' . $v->escola_nome : '')])
            ->toArray();

        $alunosOptions = [];
        $matricula = null;

        if ($turmaId) {
            abort_unless($escopo->professorAcessaTurma($prof, $turmaId), 403);

            $alunosOptions = Matricula::query()
                ->where('turma_id', $turmaId)
                ->where('situacao', 'ativa')
                ->with(['aluno.usuario'])
                ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
                ->join('users', 'alunos.user_id', '=', 'users.id')
                ->orderBy('users.nome')
                ->select('matriculas.*')
                ->get()
                ->mapWithKeys(fn ($m) => [(int) $m->id => ($m->aluno?->usuario?->nome ?? '—')])
                ->toArray();
        }

        if ($matriculaId) {
            $matricula = Matricula::with(['turma', 'aluno.usuario'])->findOrFail($matriculaId);
            abort_unless($matricula->isAtiva(), 404);
            abort_unless($escopo->professorAcessaTurma($prof, (int) $matricula->turma_id), 403);
            $turmaId = (int) $matricula->turma_id;
            $alunosOptions = $alunosOptions ?: [$matricula->id => ($matricula->aluno?->usuario?->nome ?? '—')];
        }

        return view('professor.anotacoes.create', compact(
            'turmasOptions',
            'alunosOptions',
            'turmaId',
            'matriculaId',
            'periodoAtual',
            'matricula'
        ));
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $data = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'matricula_id' => 'required|exists:matriculas,id',
            'assunto' => 'required|string|max:180',
            'texto' => 'required|string|max:5000',
        ]);

        $matricula = Matricula::with(['turma', 'aluno'])->findOrFail((int) $data['matricula_id']);
        abort_unless($matricula->isAtiva(), 404);
        abort_unless((int) $matricula->turma_id === (int) $data['turma_id'], 422);
        abort_unless($escopo->professorAcessaTurma($prof, (int) $matricula->turma_id), 403);

        $anotacao = AnotacaoProfessor::create([
            'professor_id' => $prof->id,
            'turma_id' => (int) $data['turma_id'],
            'matricula_id' => (int) $data['matricula_id'],
            'aluno_id' => (int) $matricula->aluno_id,
            'periodo' => $periodoAtual,
            'assunto' => $data['assunto'],
            'texto' => $data['texto'],
        ]);

        return redirect()->route('professor.anotacoes.show', $anotacao)->with('success', 'Anotação criada.');
    }

    public function show(AnotacaoProfessor $anotacaoProfessor)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $anotacaoProfessor->professor_id === (int) $prof->id, 403);
        $anotacaoProfessor->load(['turma', 'matricula.aluno.usuario']);

        return view('professor.anotacoes.show', ['anotacao' => $anotacaoProfessor]);
    }

    public function edit(AnotacaoProfessor $anotacaoProfessor)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $anotacaoProfessor->professor_id === (int) $prof->id, 403);
        $anotacaoProfessor->load(['turma', 'matricula.aluno.usuario']);

        return view('professor.anotacoes.edit', ['anotacao' => $anotacaoProfessor]);
    }

    public function update(Request $request, AnotacaoProfessor $anotacaoProfessor)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $anotacaoProfessor->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'assunto' => 'required|string|max:180',
            'texto' => 'required|string|max:5000',
        ]);

        $anotacaoProfessor->update($data);

        return redirect()->route('professor.anotacoes.show', $anotacaoProfessor)->with('success', 'Anotação atualizada.');
    }

    public function destroy(AnotacaoProfessor $anotacaoProfessor)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $anotacaoProfessor->professor_id === (int) $prof->id, 403);

        $anotacaoProfessor->delete();

        return redirect()->route('professor.anotacoes.index')->with('success', 'Anotação removida.');
    }
}

