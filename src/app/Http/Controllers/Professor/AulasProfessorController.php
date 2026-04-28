<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Aula, ConteudoAula, Disciplina, Turma};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AulasProfessorController extends Controller
{
    private function disciplinaPolivalenteIdParaTurma(Turma $turma): int
    {
        $turma->loadMissing('escola');
        $municipioId = (int) ($turma->escola?->municipio_id ?? 0);

        $disc = Disciplina::firstOrCreate(
            ['municipio_id' => $municipioId, 'nome' => 'Polivalente'],
            ['sigla' => 'POLI', 'ativo' => false]
        );

        return (int) $disc->id;
    }

    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
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

        if (! $request->filled('turma_id') && $vinculos->count() === 1) {
            $v = $vinculos->first();

            if ((bool) $v->turma_polivalente) {
                return redirect()->route('professor.aulas.index', ['turma_id' => $v->turma_id]);
            }

            return redirect()->route('professor.aulas.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]);
        }

        $aulas = null;
        $turmaSelecionada = null;
        if ($request->filled('turma_id')) {
            $tid = (int) $request->turma_id;
            $turmaSelecionada = Turma::with('escola')->findOrFail($tid);

            if ($turmaSelecionada->polivalente) {
                abort_unless($escopo->professorAcessaTurma($prof, $tid), 403);

                $did = $this->disciplinaPolivalenteIdParaTurma($turmaSelecionada);

                $aulas = Aula::query()
                    ->where('turma_id', $tid)
                    ->where('disciplina_id', $did)
                    ->where('professor_id', $prof->id)
                    ->where('periodo', $periodoAtual)
                    ->withCount('conteudos')
                    ->orderByDesc('data_aula')
                    ->paginate(20)
                    ->withQueryString();
            } elseif ($request->filled('disciplina_id')) {
                $did = (int) $request->disciplina_id;
                abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, $tid, $did), 403);

                $aulas = Aula::query()
                    ->where('turma_id', $tid)
                    ->where('disciplina_id', $did)
                    ->where('professor_id', $prof->id)
                    ->where('periodo', $periodoAtual)
                    ->withCount('conteudos')
                    ->orderByDesc('data_aula')
                    ->paginate(20)
                    ->withQueryString();
            }
        }

        return view('professor.aulas.index', compact('vinculos', 'aulas', 'turmaSelecionada'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'turma_id'      => 'required|exists:turmas,id',
            'disciplina_id' => 'nullable|exists:disciplinas,id',
        ]);
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);

        $turma = Turma::with('escola')->findOrFail((int) $request->turma_id);
        if ($turma->polivalente) {
            abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);
            $disciplinaId = $this->disciplinaPolivalenteIdParaTurma($turma);

            return view('professor.aulas.create', [
                'turma_id'      => (int) $turma->id,
                'disciplina_id' => $disciplinaId,
                'polivalente'   => true,
            ]);
        }

        abort_unless(
            $escopo->professorLecaDisciplinaNaTurma($prof, (int) $turma->id, (int) $request->disciplina_id),
            403
        );

        return view('professor.aulas.create', [
            'turma_id'      => (int) $request->turma_id,
            'disciplina_id' => (int) $request->disciplina_id,
            'polivalente'   => false,
        ]);
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');
        $data = $request->validate([
            'turma_id'      => 'required|exists:turmas,id',
            'disciplina_id' => 'nullable|exists:disciplinas,id',
            'data_aula'     => 'required|date',
            'hora_inicio'   => 'nullable',
            'hora_fim'      => 'nullable',
            'status'        => 'required|in:prevista,realizada,cancelada',
        ]);

        $turma = Turma::with('escola')->findOrFail((int) $data['turma_id']);
        if ($turma->polivalente) {
            abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);
            $data['disciplina_id'] = $this->disciplinaPolivalenteIdParaTurma($turma);
        } else {
            abort_unless($data['disciplina_id'], 422);
            abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, (int) $turma->id, (int) $data['disciplina_id']), 403);
        }

        Aula::create([
            'turma_id'      => $data['turma_id'],
            'disciplina_id' => $data['disciplina_id'],
            'professor_id'  => $prof->id,
            'data_aula'     => $data['data_aula'],
            'hora_inicio'   => $data['hora_inicio'] ?: null,
            'hora_fim'      => $data['hora_fim'] ?: null,
            'status'        => $data['status'],
            'periodo'       => $periodoAtual,
        ]);

        return redirect()
            ->route('professor.aulas.index', $turma->polivalente
                ? ['turma_id' => $turma->id]
                : ['turma_id' => $turma->id, 'disciplina_id' => $data['disciplina_id']]
            )
            ->with('success', 'Aula registrada.');
    }

    public function conteudo(Aula $aula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        abort_unless($escopo->aulaPertenceAoProfessor($prof, $aula), 403);

        $aula->load(['turma', 'disciplina', 'conteudos']);
        $conteudo = $aula->conteudos->first();

        return view('professor.aulas.conteudo', compact('aula', 'conteudo'));
    }

    public function salvarConteudo(Request $request, Aula $aula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        abort_unless($escopo->aulaPertenceAoProfessor($prof, $aula), 403);

        $data = $request->validate([
            'titulo'            => 'required|string|max:180',
            'descricao'         => 'nullable|string',
            'material_utilizado'=> 'nullable|string',
            'tarefa_passada'    => 'nullable|boolean',
        ]);

        ConteudoAula::updateOrCreate(
            ['aula_id' => $aula->id],
            [
                'professor_id'       => $prof->id,
                'titulo'             => $data['titulo'],
                'descricao'          => $data['descricao'] ?? null,
                'material_utilizado' => $data['material_utilizado'] ?? null,
                'tarefa_passada'     => $request->boolean('tarefa_passada'),
            ]
        );

        $aula->loadMissing('turma');

        return redirect()
            ->route('professor.aulas.index', $aula->turma?->polivalente
                ? ['turma_id' => $aula->turma_id]
                : ['turma_id' => $aula->turma_id, 'disciplina_id' => $aula->disciplina_id]
            )
            ->with('success', 'Conteúdo da aula salvo.');
    }
}
