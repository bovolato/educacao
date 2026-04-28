<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\PlanoEnsino;
use App\Models\Institucional\AnoLetivo;
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanosEnsinoProfessorController extends Controller
{
    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $planosQuery = PlanoEnsino::query()
            ->where('professor_id', $prof->id)
            ->where('periodo', $periodoAtual)
            ->with(['turma', 'disciplina', 'anoLetivo'])
            ->when($request->filled('busca'), fn ($q) => $q->where('titulo', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('turma_id'), fn ($q) => $q->where('turma_id', (int) $request->turma_id))
            ->when($request->filled('disciplina_id'), fn ($q) => $q->where('disciplina_id', (int) $request->disciplina_id))
            ->when($request->filled('ano_letivo_id'), fn ($q) => $q->where('ano_letivo_id', (int) $request->ano_letivo_id))
            ->orderByDesc('id')
            ;

        $planos = $planosQuery->paginate(15)->withQueryString();

        $turmasOptions = PlanoEnsino::query()
            ->where('professor_id', $prof->id)
            ->join('turmas', 'planos_ensino.turma_id', '=', 'turmas.id')
            ->orderBy('turmas.nome')
            ->pluck('turmas.nome', 'turmas.id');

        $disciplinasOptions = PlanoEnsino::query()
            ->where('professor_id', $prof->id)
            ->join('disciplinas', 'planos_ensino.disciplina_id', '=', 'disciplinas.id')
            ->orderBy('disciplinas.nome')
            ->pluck('disciplinas.nome', 'disciplinas.id');

        $anosOptions = AnoLetivo::where('ativo', true)->orderBy('descricao')->pluck('descricao', 'id');

        return view('professor.planos-ensino.index', compact('planos', 'turmasOptions', 'disciplinasOptions', 'anosOptions'));
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

        $anos = AnoLetivo::where('ativo', true)->orderBy('descricao')->get();

        return view('professor.planos-ensino.create', [
            'turma_id'      => $turmaId,
            'disciplina_id' => $disciplinaId,
            'disciplinasOptions' => $disciplinasOptions,
            'turmasOptions' => $turmasOptions,
            'disciplinasByTurma' => $disciplinasByTurma,
            'anos'          => $anos,
        ]);
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $data = $request->validate([
            'turma_id'       => 'required|exists:turmas,id',
            'disciplina_id'  => 'required|exists:disciplinas,id',
            'ano_letivo_id'  => 'required|exists:anos_letivos,id',
            'titulo'         => 'required|string|max:180',
            'objetivos'      => 'nullable|string',
            'metodologia'    => 'nullable|string',
            'criterios_avaliacao' => 'nullable|string',
        ]);
        abort_unless(app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, (int) $data['turma_id'], (int) $data['disciplina_id']), 403);

        PlanoEnsino::create(array_merge($data, ['professor_id' => $prof->id, 'periodo' => $periodoAtual]));

        return redirect()->route('professor.planos-ensino.index')->with('success', 'Plano de ensino criado.');
    }

    public function edit(PlanoEnsino $planoEnsino)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $planoEnsino->professor_id === (int) $prof->id, 403);
        $anos = AnoLetivo::where('ativo', true)->orderBy('descricao')->get();

        return view('professor.planos-ensino.edit', compact('planoEnsino', 'anos'));
    }

    public function update(Request $request, PlanoEnsino $planoEnsino)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $planoEnsino->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'ano_letivo_id'  => 'required|exists:anos_letivos,id',
            'titulo'         => 'required|string|max:180',
            'objetivos'      => 'nullable|string',
            'metodologia'    => 'nullable|string',
            'criterios_avaliacao' => 'nullable|string',
        ]);
        $planoEnsino->update($data);

        return redirect()->route('professor.planos-ensino.index')->with('success', 'Plano atualizado.');
    }

    public function destroy(PlanoEnsino $planoEnsino)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $planoEnsino->professor_id === (int) $prof->id, 403);
        $planoEnsino->delete();

        return redirect()->route('professor.planos-ensino.index')->with('success', 'Plano removido.');
    }
}
