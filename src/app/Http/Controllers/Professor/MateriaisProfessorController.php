<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\MaterialDidatico;
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MateriaisProfessorController extends Controller
{
    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $materiaisQuery = MaterialDidatico::query()
            ->where('professor_id', $prof->id)
            ->where('periodo', $periodoAtual)
            ->with(['turma', 'disciplina'])
            ->when($request->filled('busca'), fn ($q) => $q->where('titulo', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('turma_id'), fn ($q) => $q->where('turma_id', (int) $request->turma_id))
            ->when($request->filled('disciplina_id'), fn ($q) => $q->where('disciplina_id', (int) $request->disciplina_id))
            ->when($request->filled('visivel_aluno'), fn ($q) => $q->where('visivel_aluno', $request->visivel_aluno === '1'))
            ->orderByDesc('id')
            ;

        $materiais = $materiaisQuery->paginate(15)->withQueryString();

        $turmasOptions = MaterialDidatico::query()
            ->where('professor_id', $prof->id)
            ->join('turmas', 'materiais_didaticos.turma_id', '=', 'turmas.id')
            ->orderBy('turmas.nome')
            ->pluck('turmas.nome', 'turmas.id');

        $disciplinasOptions = MaterialDidatico::query()
            ->where('professor_id', $prof->id)
            ->join('disciplinas', 'materiais_didaticos.disciplina_id', '=', 'disciplinas.id')
            ->orderBy('disciplinas.nome')
            ->pluck('disciplinas.nome', 'disciplinas.id');

        return view('professor.materiais.index', compact('materiais', 'turmasOptions', 'disciplinasOptions'));
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

        return view('professor.materiais.create', [
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
            'link'           => 'nullable|url|max:500',
            'arquivo'        => 'nullable|string|max:255',
            'visivel_aluno'  => 'nullable|boolean',
        ]);
        abort_unless(app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, (int) $data['turma_id'], (int) $data['disciplina_id']), 403);

        MaterialDidatico::create(array_merge($data, [
            'professor_id'  => $prof->id,
            'visivel_aluno' => $request->boolean('visivel_aluno', true),
            'periodo'       => $periodoAtual,
        ]));

        return redirect()->route('professor.materiais.index')->with('success', 'Material cadastrado.');
    }

    public function edit(MaterialDidatico $materialDidatico)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $materialDidatico->professor_id === (int) $prof->id, 403);

        return view('professor.materiais.edit', compact('materialDidatico'));
    }

    public function update(Request $request, MaterialDidatico $materialDidatico)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $materialDidatico->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'titulo'        => 'required|string|max:180',
            'descricao'     => 'nullable|string',
            'link'          => 'nullable|url|max:500',
            'arquivo'       => 'nullable|string|max:255',
            'visivel_aluno' => 'nullable|boolean',
        ]);
        $materialDidatico->update(array_merge($data, [
            'visivel_aluno' => $request->boolean('visivel_aluno', true),
        ]));

        return redirect()->route('professor.materiais.index')->with('success', 'Material atualizado.');
    }

    public function destroy(MaterialDidatico $materialDidatico)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $materialDidatico->professor_id === (int) $prof->id, 403);
        $materialDidatico->delete();

        return redirect()->route('professor.materiais.index')->with('success', 'Material removido.');
    }
}
