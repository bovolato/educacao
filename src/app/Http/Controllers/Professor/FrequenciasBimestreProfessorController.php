<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Disciplina, FrequenciaBimestre, FrequenciaBimestreItem, Matricula, Turma};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrequenciasBimestreProfessorController extends Controller
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
            return redirect()->route('professor.frequencias-bimestre.index', (bool) $v->turma_polivalente
                ? ['turma_id' => $v->turma_id]
                : ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]
            );
        }

        $turmaSelecionada = null;
        $lista = null;

        if ($request->filled('turma_id')) {
            $tid = (int) $request->turma_id;
            $turmaSelecionada = Turma::with('escola')->findOrFail($tid);

            $disciplinaId = $turmaSelecionada->polivalente
                ? $this->disciplinaPolivalenteIdParaTurma($turmaSelecionada)
                : ($request->filled('disciplina_id') ? (int) $request->disciplina_id : null);

            if ($turmaSelecionada->polivalente) {
                abort_unless($escopo->professorAcessaTurma($prof, $tid), 403);
            } else {
                abort_unless($disciplinaId, 422);
                abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, $tid, $disciplinaId), 403);
            }

            $lista = FrequenciaBimestre::query()
                ->where('professor_id', $prof->id)
                ->where('turma_id', $tid)
                ->where('disciplina_id', $disciplinaId)
                ->where('periodo', $periodoAtual)
                ->withCount('itens')
                ->first();
        }

        return view('professor.frequencias-bimestre.index', compact('vinculos', 'turmaSelecionada', 'lista', 'periodoAtual'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'turma_id'      => 'required|exists:turmas,id',
            'disciplina_id' => 'nullable|exists:disciplinas,id',
        ]);

        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $turma = Turma::with('escola')->findOrFail((int) $request->turma_id);
        $disciplinaId = $turma->polivalente
            ? $this->disciplinaPolivalenteIdParaTurma($turma)
            : (int) $request->disciplina_id;

        if ($turma->polivalente) {
            abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);
        } else {
            abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, (int) $turma->id, $disciplinaId), 403);
        }

        return view('professor.frequencias-bimestre.create', [
            'turma' => $turma,
            'disciplina' => Disciplina::find($disciplinaId),
            'disciplina_id' => $disciplinaId,
            'periodoAtual' => $periodoAtual,
        ]);
    }

    public function store(Request $request)
    {
        $prof = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $data = $request->validate([
            'turma_id'      => 'required|exists:turmas,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'data_inicio'   => 'required|date',
            'data_fim'      => 'required|date|after_or_equal:data_inicio',
        ]);

        $turma = Turma::with('escola')->findOrFail((int) $data['turma_id']);
        $disciplinaId = $turma->polivalente
            ? $this->disciplinaPolivalenteIdParaTurma($turma)
            : (int) $data['disciplina_id'];

        if ($turma->polivalente) {
            abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);
        } else {
            abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, (int) $turma->id, $disciplinaId), 403);
        }

        // Se já existir lista para (professor, turma, disciplina, bimestre), reaproveita e atualiza datas.
        $lista = FrequenciaBimestre::updateOrCreate(
            [
                'professor_id' => $prof->id,
                'turma_id' => $turma->id,
                'disciplina_id' => $disciplinaId,
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
            // Cria itens faltantes sem duplicar nem apagar dados já lançados.
            FrequenciaBimestreItem::firstOrCreate(
                [
                    'frequencia_bimestre_id' => $lista->id,
                    'matricula_id' => $m->id,
                ],
                [
                    'aluno_id' => $m->aluno_id,
                    'presencas' => 0,
                    'faltas' => 0,
                    'faltas_justificadas' => 0,
                ]
            );
        }

        $msg = $lista->wasRecentlyCreated ? 'Lista de presença do bimestre criada.' : 'Lista do bimestre já existia — datas atualizadas.';
        return redirect()->route('professor.frequencias.edit', $lista)->with('success', $msg);
    }

    public function edit(FrequenciaBimestre $frequenciaBimestre)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $frequenciaBimestre->professor_id === (int) $prof->id, 403);

        $frequenciaBimestre->load(['turma.escola', 'disciplina']);

        $itens = $frequenciaBimestre->itens()
            ->with(['matricula.aluno.pessoa'])
            ->join('matriculas', 'frequencias_bimestre_itens.matricula_id', '=', 'matriculas.id')
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('frequencias_bimestre_itens.*')
            ->get();

        return view('professor.frequencias-bimestre.edit', compact('frequenciaBimestre', 'itens'));
    }

    public function update(Request $request, FrequenciaBimestre $frequenciaBimestre)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $frequenciaBimestre->professor_id === (int) $prof->id, 403);

        $data = $request->validate([
            'itens' => 'required|array',
            'itens.*.id' => 'required|exists:frequencias_bimestre_itens,id',
            'itens.*.presencas' => 'required|integer|min:0|max:999',
            'itens.*.faltas' => 'required|integer|min:0|max:999',
            'itens.*.faltas_justificadas' => 'required|integer|min:0|max:999',
            'itens.*.observacao' => 'nullable|string|max:2000',
        ]);

        foreach ($data['itens'] as $row) {
            $item = FrequenciaBimestreItem::findOrFail((int) $row['id']);
            abort_unless((int) $item->frequencia_bimestre_id === (int) $frequenciaBimestre->id, 403);

            $item->update([
                'presencas' => (int) $row['presencas'],
                'faltas' => (int) $row['faltas'],
                'faltas_justificadas' => (int) $row['faltas_justificadas'],
                'observacao' => $row['observacao'] ?? null,
            ]);
        }

        return redirect()->route('professor.frequencias.edit', $frequenciaBimestre)->with('success', 'Frequência do bimestre salva.');
    }
}

