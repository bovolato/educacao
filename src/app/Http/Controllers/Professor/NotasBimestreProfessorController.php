<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Disciplina, Matricula, NotaBimestre, NotaBimestreItem, NotaBimestreItemDisciplina, Turma};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotasBimestreProfessorController extends Controller
{
    private function vinculosProfessor($prof)
    {
        return DB::table('turma_professores as tp')
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
    }

    public function index(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $vinculos = $this->vinculosProfessor($prof);

        if (! $request->filled('turma_id') && $vinculos->count() === 1) {
            $v = $vinculos->first();
            return redirect()->route('professor.notas-bimestre.index', ['turma_id' => $v->turma_id]);
        }

        $turmaSelecionada = null;
        $lista = null;

        if ($request->filled('turma_id')) {
            $tid = (int) $request->turma_id;
            $turmaSelecionada = Turma::with('escola')->findOrFail($tid);
            abort_unless($escopo->professorAcessaTurma($prof, $tid), 403);

            $lista = NotaBimestre::query()
                ->where('professor_id', $prof->id)
                ->where('turma_id', $tid)
                ->where('periodo', $periodoAtual)
                ->withCount('itens')
                ->first();
        }

        return view('professor.notas-bimestre.index', compact('vinculos', 'turmaSelecionada', 'lista', 'periodoAtual'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
        ]);

        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $turma = Turma::with('escola')->findOrFail((int) $request->turma_id);
        abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);

        $disciplinas = Disciplina::query()
            ->join('turma_professores as tp', 'tp.disciplina_id', '=', 'disciplinas.id')
            ->where('tp.professor_id', $prof->id)
            ->where('tp.turma_id', $turma->id)
            ->orderBy('disciplinas.nome')
            ->select('disciplinas.*')
            ->distinct()
            ->get();

        return view('professor.notas-bimestre.create', [
            'turma' => $turma,
            'periodoAtual' => $periodoAtual,
            'disciplinas' => $disciplinas,
        ]);
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $data = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
        ]);

        $turma = Turma::with('escola')->findOrFail((int) $data['turma_id']);
        abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);

        $disciplinas = Disciplina::query()
            ->join('turma_professores as tp', 'tp.disciplina_id', '=', 'disciplinas.id')
            ->where('tp.professor_id', $prof->id)
            ->where('tp.turma_id', $turma->id)
            ->orderBy('disciplinas.nome')
            ->select('disciplinas.*')
            ->distinct()
            ->get();

        abort_unless($disciplinas->count() > 0, 403);

        $lista = NotaBimestre::updateOrCreate(
            [
                'professor_id' => $prof->id,
                'turma_id' => $turma->id,
                'periodo' => $periodoAtual,
            ],
            [
                'data_inicio' => $data['data_inicio'],
                'data_fim' => $data['data_fim'],
            ]
        );

        $matriculas = Matricula::query()
            ->where('turma_id', $turma->id)
            ->where('situacao', 'ativa')
            ->with(['aluno.pessoa'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('matriculas.*')
            ->get();

        foreach ($matriculas as $m) {
            $item = NotaBimestreItem::firstOrCreate(
                [
                    'nota_bimestre_id' => $lista->id,
                    'matricula_id' => $m->id,
                ],
                [
                    'aluno_id' => $m->aluno_id,
                ]
            );

            foreach ($disciplinas as $d) {
                NotaBimestreItemDisciplina::firstOrCreate(
                    [
                        'nota_bimestre_item_id' => $item->id,
                        'disciplina_id' => $d->id,
                    ],
                    [
                        'nota' => null,
                    ]
                );
            }
        }

        $msg = $lista->wasRecentlyCreated ? 'Lista de notas do bimestre criada.' : 'Lista do bimestre já existia — datas atualizadas.';
        return redirect()->route('professor.notas-bimestre.edit', $lista)->with('success', $msg);
    }

    public function edit(NotaBimestre $notaBimestre)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $notaBimestre->professor_id === (int) $prof->id, 403);

        $notaBimestre->load(['turma.escola']);

        $disciplinas = Disciplina::query()
            ->join('turma_professores as tp', 'tp.disciplina_id', '=', 'disciplinas.id')
            ->where('tp.professor_id', $prof->id)
            ->where('tp.turma_id', $notaBimestre->turma_id)
            ->orderBy('disciplinas.nome')
            ->select('disciplinas.*')
            ->distinct()
            ->get();

        $itens = $notaBimestre->itens()
            ->with(['matricula.aluno.pessoa', 'disciplinas'])
            ->join('matriculas', 'notas_bimestre_itens.matricula_id', '=', 'matriculas.id')
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('notas_bimestre_itens.*')
            ->get();

        // Garante que cada item tenha registro para cada disciplina (caso vínculos mudem).
        foreach ($itens as $item) {
            foreach ($disciplinas as $d) {
                NotaBimestreItemDisciplina::firstOrCreate(
                    [
                        'nota_bimestre_item_id' => $item->id,
                        'disciplina_id' => $d->id,
                    ],
                    [
                        'nota' => null,
                    ]
                );
            }
        }

        // Recarrega com disciplinas completas
        $itens->load('disciplinas');

        return view('professor.notas-bimestre.edit', compact('notaBimestre', 'itens', 'disciplinas'));
    }

    public function update(Request $request, NotaBimestre $notaBimestre)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $notaBimestre->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'itens' => 'required|array',
            'itens.*.id' => 'required|exists:notas_bimestre_itens,id',
            'itens.*.media_final' => 'nullable|numeric|min:0|max:1000',
            'itens.*.observacao' => 'nullable|string|max:2000',
            'itens.*.notas' => 'nullable|array',
        ]);

        foreach ($data['itens'] as $row) {
            $item = NotaBimestreItem::with('disciplinas')->findOrFail((int) $row['id']);
            abort_unless((int) $item->nota_bimestre_id === (int) $notaBimestre->id, 403);

            $item->update([
                'media_final' => array_key_exists('media_final', $row) && $row['media_final'] !== null ? (float) $row['media_final'] : null,
                'observacao' => $row['observacao'] ?? null,
            ]);

            $notas = $row['notas'] ?? [];
            foreach ($notas as $discId => $valor) {
                $discIdInt = (int) $discId;
                $model = NotaBimestreItemDisciplina::firstOrCreate(
                    [
                        'nota_bimestre_item_id' => $item->id,
                        'disciplina_id' => $discIdInt,
                    ]
                );
                $model->update([
                    'nota' => ($valor === '' || $valor === null) ? null : (float) $valor,
                ]);
            }
        }

        return redirect()->route('professor.notas-bimestre.edit', $notaBimestre)->with('success', 'Notas do bimestre salvas.');
    }
}

