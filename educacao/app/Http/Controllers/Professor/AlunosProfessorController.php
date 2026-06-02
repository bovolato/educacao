<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{AnotacaoProfessor, Avaliacao, Boletim, Disciplina, Frequencia, FrequenciaBimestre, FrequenciaBimestreItem, Matricula, Nota, NotaBimestre, NotaBimestreItem, Tarefa, TarefaRegistroAluno, Turma};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunosProfessorController extends Controller
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
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);

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
                return redirect()->route('professor.alunos.index', ['turma_id' => $v->turma_id]);
            }

            return redirect()->route('professor.alunos.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]);
        }

        $matriculas = null;
        $turmaSelecionada = null;

        // Lista “geral” para facilitar achar o aluno rapidamente.
        $turmaIdsProfessor = $prof->turmas()->pluck('turmas.id')->map(fn ($v) => (int) $v);

        $matriculasQuery = Matricula::query()
            ->where('situacao', 'ativa')
            ->whereIn('turma_id', $turmaIdsProfessor)
            ->with(['aluno.usuario', 'turma.escola'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('users', 'alunos.user_id', '=', 'users.id')
            ->join('turmas', 'matriculas.turma_id', '=', 'turmas.id')
            ->when($request->filled('turma_id'), function ($q) use ($request, $escopo, $prof, &$turmaSelecionada) {
                $tid = (int) $request->turma_id;
                $turmaSelecionada = Turma::with('escola')->findOrFail($tid);
                abort_unless($escopo->professorAcessaTurma($prof, $tid), 403);
                return $q->where('matriculas.turma_id', $tid);
            })
            ->when($request->filled('busca'), fn ($q) => $q->where('users.nome', 'like', '%' . $request->busca . '%'))
            ->orderBy('turmas.nome')
            ->orderBy('users.nome')
            ->select('matriculas.*');

        $matriculas = $matriculasQuery->paginate(25)->withQueryString();

        return view('professor.alunos.index', compact('vinculos', 'matriculas', 'turmaSelecionada'));
    }

    public function show(Request $request, Matricula $matricula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        $periodoAtual = $request->session()->get('professor_periodo', '1B');

        $matricula->load(['turma.escola', 'aluno.usuario', 'aluno.responsaveis.usuario']);
        abort_unless($matricula->isAtiva(), 404);

        $turma = $matricula->turma;
        abort_unless($turma !== null, 404);

        if ($turma->polivalente) {
            abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);
        } else {
            abort_unless($escopo->professorAcessaTurma($prof, (int) $turma->id), 403);
        }

        $disciplinaIds = DB::table('turma_professores')
            ->where('professor_id', $prof->id)
            ->where('turma_id', $turma->id)
            ->pluck('disciplina_id')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        $disciplinas = Disciplina::query()
            ->whereIn('id', $disciplinaIds)
            ->orderBy('nome')
            ->get()
            ->keyBy('id');

        // Frequência (revertida): fonte da verdade é por aula (tabela frequencias).
        // Polivalente: usa a disciplina "Polivalente" sentinela; não-polivalente: soma disciplinas do professor na turma.
        $didFreqs = $turma->polivalente ? collect([$this->disciplinaPolivalenteIdParaTurma($turma)]) : $disciplinaIds;
        $freqResumo = null;
        $ultimasFrequencias = collect();

        if ($didFreqs->isNotEmpty()) {
            $freqBase = Frequencia::query()
                ->join('aulas', 'frequencias.aula_id', '=', 'aulas.id')
                ->where('frequencias.matricula_id', $matricula->id)
                ->where('aulas.turma_id', $turma->id)
                ->whereIn('aulas.disciplina_id', $didFreqs)
                ->where('aulas.periodo', $periodoAtual)
                ->select(['frequencias.*', 'aulas.data_aula'])
                ->orderByDesc('aulas.data_aula');

            // Query agregada precisa selecionar apenas colunas agregadas (compatível com ONLY_FULL_GROUP_BY).
            $freqResumo = Frequencia::query()
                ->join('aulas', 'frequencias.aula_id', '=', 'aulas.id')
                ->where('frequencias.matricula_id', $matricula->id)
                ->where('aulas.turma_id', $turma->id)
                ->whereIn('aulas.disciplina_id', $didFreqs)
                ->where('aulas.periodo', $periodoAtual)
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='presente' THEN 1 ELSE 0 END) as presentes")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='falta' THEN 1 ELSE 0 END) as faltas")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='justificada' THEN 1 ELSE 0 END) as justificadas")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='atraso' THEN 1 ELSE 0 END) as atrasos")
                ->first();

            $ultimasFrequencias = $freqBase->limit(20)->get();
        }

        // Avaliações e notas (todas as disciplinas do professor na turma).
        // A fonte da verdade agora é o bimestre global (sessão).
        $avaliacoesTodas = Avaliacao::query()
            ->where('turma_id', $turma->id)
            ->where('professor_id', $prof->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->with('disciplina')
            ->orderByDesc('data_avaliacao')
            ->get();

        $periodos = collect(['1B','2B','3B','4B']);
        $periodoSelecionado = $periodoAtual;
        $avaliacoes = $avaliacoesTodas->where('periodo', $periodoSelecionado)->values();

        $notasPorAvaliacao = Nota::query()
            ->whereIn('avaliacao_id', $avaliacoes->pluck('id'))
            ->where('matricula_id', $matricula->id)
            ->get()
            ->keyBy('avaliacao_id');

        // Média geral calculada (todas as avaliações / todas as disciplinas do professor na turma).
        $mediaGeralCalculada = null;
        $somaPesoGeral = 0.0;
        $somaGeral = 0.0;
        foreach ($avaliacoes as $av) {
            $n = $notasPorAvaliacao->get($av->id);
            if (! $n || $n->falta_na_avaliacao || $n->nota === null) {
                continue;
            }
            $peso = (float) ($av->valor ?? 10);
            $somaPesoGeral += $peso;
            $somaGeral += ((float) $n->nota) * $peso;
        }
        if ($somaPesoGeral > 0) {
            $mediaGeralCalculada = round($somaGeral / $somaPesoGeral, 2);
        }

        // Médias calculadas por disciplina (ponderada pelo valor).
        $mediaPorDisciplina = [];
        foreach ($avaliacoes->groupBy('disciplina_id') as $did => $avs) {
            $somaPeso = 0.0;
            $soma = 0.0;
            foreach ($avs as $av) {
                $n = $notasPorAvaliacao->get($av->id);
                if (! $n || $n->falta_na_avaliacao || $n->nota === null) {
                    continue;
                }
                $peso = (float) ($av->valor ?? 10);
                $somaPeso += $peso;
                $soma += ((float) $n->nota) * $peso;
            }
            $mediaPorDisciplina[(int) $did] = $somaPeso > 0 ? round($soma / $somaPeso, 2) : null;
        }

        $boletins = Boletim::query()
            ->where('matricula_id', $matricula->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->where('periodo', $periodoSelecionado)
            ->get()
            ->keyBy('disciplina_id');

        // Média geral manual: média simples das médias manuais por disciplina (boletim.media).
        // Isso reflete exatamente os valores digitados nos cards (ex.: 5, 8, 2, 0 => 3,75).
        $mediaGeralManual = null;
        $mediasManuais = $boletins->pluck('media')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v)->values();
        if ($mediasManuais->count() > 0) {
            $mediaGeralManual = round($mediasManuais->avg(), 2);
        }

        // Notas do bimestre (novo): se existir e tiver valores lançados, tem prioridade para exibição no resumão.
        $notasBimestreItem = null;
        $notasBimestrePorDisciplina = [];
        $mediaFinalBimestre = null;
        $usarNotasBimestre = false;
        $mediaGeralManualComNotasBimestre = $mediaGeralManual;
        $listaNotasBimestre = NotaBimestre::query()
            ->where('professor_id', $prof->id)
            ->where('turma_id', $turma->id)
            ->where('periodo', $periodoSelecionado)
            ->first();

        if ($listaNotasBimestre) {
            $notasBimestreItem = NotaBimestreItem::query()
                ->where('nota_bimestre_id', $listaNotasBimestre->id)
                ->where('matricula_id', $matricula->id)
                ->with('disciplinas')
                ->first();

            if ($notasBimestreItem) {
                $mediaFinalBimestre = $notasBimestreItem->media_final !== null ? (float) $notasBimestreItem->media_final : null;
                $notasBimestrePorDisciplina = $notasBimestreItem->disciplinas
                    ->filter(fn ($r) => $r->nota !== null)
                    ->mapWithKeys(fn ($r) => [(int) $r->disciplina_id => (float) $r->nota])
                    ->toArray();

                $usarNotasBimestre = $mediaFinalBimestre !== null || count($notasBimestrePorDisciplina) > 0;
            }
        }

        // Média manual (geral) com prioridade para o valor digitado no boletim,
        // mas usando a Nota do bimestre quando não houver média manual na disciplina.
        if ($usarNotasBimestre) {
            $vals = [];
            foreach ($disciplinaIds as $did) {
                $did = (int) $did;
                $manual = $boletins->get($did)?->media;
                if ($manual !== null && $manual !== '') {
                    $vals[] = (float) $manual;
                    continue;
                }
                if (array_key_exists($did, $notasBimestrePorDisciplina) && $notasBimestrePorDisciplina[$did] !== null) {
                    $vals[] = (float) $notasBimestrePorDisciplina[$did];
                }
            }
            if (count($vals) > 0) {
                $mediaGeralManualComNotasBimestre = round(collect($vals)->avg(), 2);
            }
        }

        // Tarefas (todas as disciplinas do professor na turma)
        $tarefas = Tarefa::query()
            ->where('turma_id', $turma->id)
            ->where('professor_id', $prof->id)
            ->whereIn('disciplina_id', $disciplinaIds)
            ->where('periodo', $periodoSelecionado)
            ->with('disciplina')
            ->orderByDesc('data_entrega')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $tarefasRegistro = TarefaRegistroAluno::query()
            ->where('matricula_id', $matricula->id)
            ->whereIn('tarefa_id', $tarefas->pluck('id'))
            ->get()
            ->keyBy('tarefa_id');

        // Anotações do professor sobre o aluno (por bimestre, somente autor).
        $anotacoesAluno = AnotacaoProfessor::query()
            ->where('professor_id', $prof->id)
            ->where('matricula_id', $matricula->id)
            ->where('periodo', $periodoSelecionado)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('professor.alunos.show', compact(
            'matricula',
            'turma',
            'disciplinas',
            'freqResumo',
            'ultimasFrequencias',
            'avaliacoes',
            'notasPorAvaliacao',
            'mediaGeralCalculada',
            'usarNotasBimestre',
            'notasBimestrePorDisciplina',
            'mediaFinalBimestre',
            'mediaGeralManualComNotasBimestre',
            'mediaGeralManual',
            'mediaPorDisciplina',
            'periodos',
            'periodoSelecionado',
            'boletins',
            'tarefas',
            'tarefasRegistro',
            'anotacoesAluno'
        ));
    }

    public function salvarMediaManual(Request $request, Matricula $matricula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);

        $matricula->load('turma');
        abort_unless($matricula->isAtiva(), 404);
        abort_unless($matricula->turma !== null, 404);
        abort_unless($escopo->professorAcessaTurma($prof, (int) $matricula->turma_id), 403);

        $data = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'periodo'       => 'required|string|max:20',
            'media'         => 'nullable|numeric|min:0|max:1000',
        ]);
        abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, (int) $matricula->turma_id, (int) $data['disciplina_id']), 403);

        Boletim::updateOrCreate(
            [
                'matricula_id' => $matricula->id,
                'disciplina_id'=> (int) $data['disciplina_id'],
                'periodo'      => $data['periodo'],
            ],
            [
                'media' => $data['media'] !== '' && $data['media'] !== null ? $data['media'] : null,
            ]
        );

        return redirect()->route('professor.alunos.show', $matricula)->with('success', 'Média manual salva.');
    }

    public function salvarTarefaRegistro(Request $request, Matricula $matricula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);

        $matricula->load('turma');
        abort_unless($matricula->isAtiva(), 404);
        abort_unless($matricula->turma !== null, 404);
        abort_unless($escopo->professorAcessaTurma($prof, (int) $matricula->turma_id), 403);

        $data = $request->validate([
            'tarefa_id'   => 'required|exists:tarefas,id',
            'status'      => 'required|in:pendente,entregue,nao_entregue',
            'observacao'  => 'nullable|string|max:1000',
            'disciplina_id' => 'nullable|integer',
            'periodo'       => 'nullable|string',
        ]);

        $tarefa = Tarefa::findOrFail((int) $data['tarefa_id']);
        abort_unless((int) $tarefa->turma_id === (int) $matricula->turma_id, 403);
        abort_unless((int) $tarefa->professor_id === (int) $prof->id, 403);
        abort_unless($escopo->professorLecaDisciplinaNaTurma($prof, (int) $matricula->turma_id, (int) $tarefa->disciplina_id), 403);

        TarefaRegistroAluno::updateOrCreate(
            ['tarefa_id' => $tarefa->id, 'matricula_id' => $matricula->id],
            [
                'professor_id' => $prof->id,
                'status'       => $data['status'],
                'observacao'   => $data['observacao'] ?? null,
            ]
        );

        $backQuery = array_filter([
            'disciplina_id' => $request->input('disciplina_id'),
            'periodo'       => $request->input('periodo'),
        ]);

        return redirect()->route('professor.alunos.show', $matricula)->with('success', 'Registro da tarefa salvo.')->with('backQuery', $backQuery);
    }
}

