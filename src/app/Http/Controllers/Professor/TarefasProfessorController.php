<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\Matricula;
use App\Models\Academico\Tarefa;
use App\Models\Academico\TarefaRegistroAluno;
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarefasProfessorController extends Controller
{
    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $tarefasQuery = Tarefa::query()
            ->where('professor_id', $prof->id)
            ->where('periodo', $periodoAtual)
            ->with(['turma', 'disciplina'])
            ->when($request->filled('busca'), fn ($q) => $q->where('titulo', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('turma_id'), fn ($q) => $q->where('turma_id', (int) $request->turma_id))
            ->when($request->filled('disciplina_id'), fn ($q) => $q->where('disciplina_id', (int) $request->disciplina_id))
            ->when($request->filled('entrega_de'), fn ($q) => $q->whereDate('data_entrega', '>=', $request->entrega_de))
            ->when($request->filled('entrega_ate'), fn ($q) => $q->whereDate('data_entrega', '<=', $request->entrega_ate))
            ->orderByDesc('data_entrega')
            ->orderByDesc('id');

        $tarefas = $tarefasQuery->paginate(15)->withQueryString();

        $turmasOptions = Tarefa::query()
            ->where('professor_id', $prof->id)
            ->join('turmas', 'tarefas.turma_id', '=', 'turmas.id')
            ->orderBy('turmas.nome')
            ->pluck('turmas.nome', 'turmas.id');

        $disciplinasOptions = Tarefa::query()
            ->where('professor_id', $prof->id)
            ->join('disciplinas', 'tarefas.disciplina_id', '=', 'disciplinas.id')
            ->orderBy('disciplinas.nome')
            ->pluck('disciplinas.nome', 'disciplinas.id');

        return view('professor.tarefas.index', compact('tarefas', 'turmasOptions', 'disciplinasOptions'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'turma_id'      => 'nullable|exists:turmas,id',
            'disciplina_id' => 'nullable|exists:disciplinas,id',
        ]);
        $prof = auth()->user()->professor;
        $turmaId = $request->filled('turma_id') ? (int) $request->turma_id : null;
        $disciplinaId = $request->filled('disciplina_id') ? (int) $request->disciplina_id : null;

        $vinculos = DB::table('turma_professores as tp')
            ->where('tp.professor_id', $prof->id)
            ->join('turmas as t', 't.id', '=', 'tp.turma_id')
            ->join('disciplinas as d', 'd.id', '=', 'tp.disciplina_id')
            ->orderBy('t.nome')
            ->orderBy('d.nome')
            ->get([
                't.id as turma_id',
                't.nome as turma_nome',
                'd.id as disciplina_id',
                'd.nome as disciplina_nome',
            ]);

        abort_unless($vinculos->count() > 0, 403);

        $turmasOptions = $vinculos->pluck('turma_nome', 'turma_id')->unique();
        $disciplinasByTurma = $vinculos
            ->groupBy('turma_id')
            ->map(fn ($rows) => $rows->pluck('disciplina_nome', 'disciplina_id'));

        $disciplinasOptions = collect();
        if ($turmaId) {
            $disciplinasOptions = $disciplinasByTurma->get($turmaId, collect());
            abort_unless($disciplinasOptions->count() > 0, 403);
            if ($disciplinaId) {
                abort_unless(app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, $turmaId, $disciplinaId), 403);
            }
        }

        return view('professor.tarefas.create', [
            'turma_id'      => $turmaId,
            'disciplina_id' => $disciplinaId,
            'disciplinasOptions' => $disciplinasOptions,
            'turmasOptions' => $turmasOptions,
            'disciplinasByTurma' => $disciplinasByTurma,
        ]);
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $data = $request->validate([
            'turma_id'       => 'required|exists:turmas,id',
            'disciplina_id'  => 'required|exists:disciplinas,id',
            'titulo'         => 'required|string|max:180',
            'descricao'      => 'nullable|string',
            'data_postagem'  => 'nullable|date',
            'data_entrega'   => 'nullable|date',
            'valor'          => 'nullable|numeric|min:0|max:1000',
        ]);
        abort_unless(app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, (int) $data['turma_id'], (int) $data['disciplina_id']), 403);

        Tarefa::create(array_merge($data, ['professor_id' => $prof->id, 'periodo' => $periodoAtual]));

        return redirect()->route('professor.tarefas.index')->with('success', 'Tarefa criada.');
    }

    public function edit(Tarefa $tarefa)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $tarefa->professor_id === (int) $prof->id, 403);

        $tarefa->load('turma');

        $matriculas = $tarefa->turma->matriculasAtivas()
            ->with(['aluno.pessoa'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('matriculas.*')
            ->get();

        $registros = TarefaRegistroAluno::query()
            ->where('tarefa_id', $tarefa->id)
            ->get()
            ->keyBy('matricula_id');

        return view('professor.tarefas.edit', compact('tarefa', 'matriculas', 'registros'));
    }

    public function update(Request $request, Tarefa $tarefa)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $tarefa->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'titulo'        => 'required|string|max:180',
            'descricao'     => 'nullable|string',
            'data_postagem' => 'nullable|date',
            'data_entrega'  => 'nullable|date',
            'valor'         => 'nullable|numeric|min:0|max:1000',
            'fez'           => 'nullable|array',
            'fez.*'         => 'integer|exists:matriculas,id',
        ]);
        $tarefa->update($data);

        // Checklist simples: "fez" por aluno (checkbox).
        // Regra: checked => status=fez; unchecked só "desmarca" se antes era "fez" (não sobrescreve outros status mais ricos como "entregue").
        $idsMarcados = collect($request->input('fez', []))->map(fn ($v) => (int) $v)->unique()->values();

        $matriculasAtivasIds = Matricula::query()
            ->where('turma_id', $tarefa->turma_id)
            ->where('situacao', 'ativa')
            ->pluck('id')
            ->map(fn ($v) => (int) $v);

        $idsMarcados = $idsMarcados->intersect($matriculasAtivasIds)->values();

        $existentes = TarefaRegistroAluno::query()
            ->where('tarefa_id', $tarefa->id)
            ->whereIn('matricula_id', $matriculasAtivasIds)
            ->get()
            ->keyBy('matricula_id');

        foreach ($matriculasAtivasIds as $mid) {
            $marcado = $idsMarcados->contains((int) $mid);
            $reg = $existentes->get((int) $mid);

            if ($marcado) {
                TarefaRegistroAluno::updateOrCreate(
                    ['tarefa_id' => $tarefa->id, 'matricula_id' => (int) $mid],
                    ['professor_id' => $prof->id, 'status' => 'fez']
                );
                continue;
            }

            if ($reg && $reg->status === 'fez') {
                $reg->update(['status' => 'pendente']);
            }
        }

        return redirect()->route('professor.tarefas.index')->with('success', 'Tarefa atualizada.');
    }

    public function destroy(Tarefa $tarefa)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $tarefa->professor_id === (int) $prof->id, 403);
        $tarefa->delete();

        return redirect()->route('professor.tarefas.index')->with('success', 'Tarefa removida.');
    }
}
