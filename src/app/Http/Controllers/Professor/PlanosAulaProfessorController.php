<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\PlanoAula;
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanosAulaProfessorController extends Controller
{
    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $planosQuery = PlanoAula::query()
            ->where('professor_id', $prof->id)
            ->where('periodo', $periodoAtual)
            ->with(['turma', 'disciplina'])
            ->when($request->filled('busca'), fn ($q) => $q->where('titulo', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('turma_id'), fn ($q) => $q->where('turma_id', (int) $request->turma_id))
            ->when($request->filled('disciplina_id'), fn ($q) => $q->where('disciplina_id', (int) $request->disciplina_id))
            ->when($request->filled('prevista_de'), fn ($q) => $q->whereDate('data_prevista', '>=', $request->prevista_de))
            ->when($request->filled('prevista_ate'), fn ($q) => $q->whereDate('data_prevista', '<=', $request->prevista_ate))
            ->orderByDesc('data_prevista')
            ->orderByDesc('id')
            ;

        $planos = $planosQuery->paginate(15)->withQueryString();

        $turmasOptions = PlanoAula::query()
            ->where('professor_id', $prof->id)
            ->join('turmas', 'planos_aula.turma_id', '=', 'turmas.id')
            ->orderBy('turmas.nome')
            ->pluck('turmas.nome', 'turmas.id');

        $disciplinasOptions = PlanoAula::query()
            ->where('professor_id', $prof->id)
            ->join('disciplinas', 'planos_aula.disciplina_id', '=', 'disciplinas.id')
            ->orderBy('disciplinas.nome')
            ->pluck('disciplinas.nome', 'disciplinas.id');

        return view('professor.planos-aula.index', compact('planos', 'turmasOptions', 'disciplinasOptions'));
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

        return view('professor.planos-aula.create', [
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
            'turma_id'         => 'required|exists:turmas,id',
            'disciplina_id'    => 'required|exists:disciplinas,id',
            'data_prevista'    => 'nullable|date',
            'titulo'           => 'required|string|max:180',
            'objetivos'        => 'nullable|string',
            'conteudo_previsto'=> 'nullable|string',
            'recursos'         => 'nullable|string',
        ]);
        abort_unless(app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, (int) $data['turma_id'], (int) $data['disciplina_id']), 403);

        PlanoAula::create(array_merge($data, ['professor_id' => $prof->id, 'periodo' => $periodoAtual]));

        return redirect()->route('professor.planos.index')->with('success', 'Plano de aula criado.');
    }

    public function edit(PlanoAula $planoAula)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $planoAula->professor_id === (int) $prof->id, 403);

        return view('professor.planos-aula.edit', compact('planoAula'));
    }

    public function update(Request $request, PlanoAula $planoAula)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $planoAula->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'data_prevista'     => 'nullable|date',
            'titulo'            => 'required|string|max:180',
            'objetivos'         => 'nullable|string',
            'conteudo_previsto' => 'nullable|string',
            'recursos'          => 'nullable|string',
        ]);
        $planoAula->update($data);

        return redirect()->route('professor.planos.index')->with('success', 'Plano de aula atualizado.');
    }

    public function destroy(PlanoAula $planoAula)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $planoAula->professor_id === (int) $prof->id, 403);
        $planoAula->delete();

        return redirect()->route('professor.planos.index')->with('success', 'Plano de aula removido.');
    }
}
