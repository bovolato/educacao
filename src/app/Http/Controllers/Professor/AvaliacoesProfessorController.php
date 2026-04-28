<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Avaliacao, Nota};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvaliacoesProfessorController extends Controller
{
    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $vinculos = DB::table('turma_professores as tp')
            ->where('tp.professor_id', $prof->id)
            ->join('turmas as t', 't.id', '=', 'tp.turma_id')
            ->join('disciplinas as d', 'd.id', '=', 'tp.disciplina_id')
            ->leftJoin('escolas as e', 'e.id', '=', 't.escola_id')
            ->orderBy('t.nome')
            ->orderBy('d.nome')
            ->select([
                't.id as turma_id', 't.nome as turma_nome',
                't.polivalente as turma_polivalente',
                'd.id as disciplina_id', 'd.nome as disciplina_nome',
                'e.nome as escola_nome',
            ])
            ->get();

        if ((! $request->filled('turma_id') || ! $request->filled('disciplina_id')) && $vinculos->count() === 1) {
            $v = $vinculos->first();

            return redirect()->route('professor.avaliacoes.index', [
                'turma_id'      => $v->turma_id,
                'disciplina_id' => $v->disciplina_id,
            ]);
        }

        $avaliacoes = null;
        if ($request->filled('turma_id') && $request->filled('disciplina_id')) {
            $tid = (int) $request->turma_id;
            $did = (int) $request->disciplina_id;
            abort_unless(app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, $tid, $did), 403);

            $avaliacoes = Avaliacao::query()
                ->where('turma_id', $tid)
                ->where('disciplina_id', $did)
                ->where('professor_id', $prof->id)
                ->where('periodo', $periodoAtual)
                ->orderByDesc('data_avaliacao')
                ->paginate(15)
                ->withQueryString();
        }

        return view('professor.avaliacoes.index', compact('vinculos', 'avaliacoes'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'turma_id'     => 'required|exists:turmas,id',
            'disciplina_id'=> 'required|exists:disciplinas,id',
        ]);
        $prof = auth()->user()->professor;
        if (! app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, (int) $request->turma_id, (int) $request->disciplina_id)) {
            return redirect()
                ->route('professor.avaliacoes.index', [
                    'turma_id' => (int) $request->turma_id,
                    'disciplina_id' => (int) $request->disciplina_id,
                ])
                ->with('error', 'Você não está vinculada a esta disciplina nesta turma.');
        }

        return view('professor.avaliacoes.create', [
            'turma_id'      => (int) $request->turma_id,
            'disciplina_id' => (int) $request->disciplina_id,
        ]);
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $data = $request->validate([
            'turma_id'       => 'required|exists:turmas,id',
            'disciplina_id'  => 'required|exists:disciplinas,id',
            'titulo'         => 'required|string|max:120',
            'tipo'           => 'nullable|string|max:40',
            'data_avaliacao' => 'nullable|date',
            'valor'          => 'nullable|numeric|min:0|max:1000',
            'descricao'      => 'nullable|string',
        ]);

        if (! app(EscopoAcesso::class)->professorLecaDisciplinaNaTurma($prof, (int) $data['turma_id'], (int) $data['disciplina_id'])) {
            return redirect()
                ->route('professor.avaliacoes.index', [
                    'turma_id' => (int) $data['turma_id'],
                    'disciplina_id' => (int) $data['disciplina_id'],
                ])
                ->with('error', 'Você não está vinculada a esta disciplina nesta turma.');
        }

        Avaliacao::create([
            'turma_id'       => $data['turma_id'],
            'disciplina_id'  => $data['disciplina_id'],
            'professor_id'   => $prof->id,
            'titulo'         => $data['titulo'],
            'tipo'           => $data['tipo'] ?? 'prova',
            'data_avaliacao' => $data['data_avaliacao'] ?? now()->toDateString(),
            'valor'          => $data['valor'] ?? 10,
            'periodo'        => $periodoAtual,
            'descricao'      => $data['descricao'] ?? null,
        ]);

        return redirect()->route('professor.avaliacoes.index', [
            'turma_id' => $data['turma_id'], 'disciplina_id' => $data['disciplina_id'],
        ])->with('success', 'Avaliação criada. Lançe as notas na tela de notas.');
    }

    public function edit(Avaliacao $avaliacao)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $avaliacao->professor_id === (int) $prof->id, 403);

        return view('professor.avaliacoes.edit', compact('avaliacao'));
    }

    public function update(Request $request, Avaliacao $avaliacao)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $avaliacao->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'titulo'         => 'required|string|max:120',
            'tipo'           => 'nullable|string|max:40',
            'data_avaliacao' => 'nullable|date',
            'valor'          => 'nullable|numeric|min:0|max:1000',
            'periodo'        => 'nullable|string|max:20',
            'descricao'      => 'nullable|string',
        ]);

        $avaliacao->update($data);

        return redirect()->route('professor.avaliacoes.index', [
            'turma_id' => $avaliacao->turma_id, 'disciplina_id' => $avaliacao->disciplina_id,
        ])->with('success', 'Avaliação atualizada.');
    }

    public function destroy(Avaliacao $avaliacao)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $avaliacao->professor_id === (int) $prof->id, 403);

        $tid = $avaliacao->turma_id;
        $did = $avaliacao->disciplina_id;
        Nota::where('avaliacao_id', $avaliacao->id)->delete();
        $avaliacao->delete();

        return redirect()->route('professor.avaliacoes.index', [
            'turma_id' => $tid, 'disciplina_id' => $did,
        ])->with('success', 'Avaliação excluída.');
    }
}
