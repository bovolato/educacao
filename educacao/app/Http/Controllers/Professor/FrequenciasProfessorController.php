<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Aula, Disciplina, Frequencia, Turma};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrequenciasProfessorController extends Controller
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
        $prof    = auth()->user()->professor;
        $escopo  = app(EscopoAcesso::class);
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
                return redirect()->route('professor.frequencias.index', ['turma_id' => $v->turma_id]);
            }

            return redirect()->route('professor.frequencias.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]);
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
                    ->orderByDesc('data_aula')
                    ->withCount('frequencias')
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
                    ->orderByDesc('data_aula')
                    ->withCount('frequencias')
                    ->paginate(20)
                    ->withQueryString();
            }
        }

        return view('professor.frequencias.index', compact('vinculos', 'aulas', 'turmaSelecionada'));
    }

    public function edit(Aula $aula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        abort_unless($escopo->aulaPertenceAoProfessor($prof, $aula), 403);

        $aula->load(['turma', 'disciplina']);

        $matriculas = $aula->turma->matriculasAtivas()
            ->with(['aluno.usuario'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('users', 'alunos.user_id', '=', 'users.id')
            ->orderBy('users.nome')
            ->select('matriculas.*')
            ->get();

        $freqPorMatricula = Frequencia::query()
            ->where('aula_id', $aula->id)
            ->get()
            ->keyBy('matricula_id');

        return view('professor.frequencias.edit', compact('aula', 'matriculas', 'freqPorMatricula'));
    }

    public function update(Request $request, Aula $aula)
    {
        $prof   = auth()->user()->professor;
        $escopo = app(EscopoAcesso::class);
        abort_unless($escopo->aulaPertenceAoProfessor($prof, $aula), 403);

        $request->validate([
            'frequencias'               => 'required|array',
            'frequencias.*.matricula_id' => 'required|exists:matriculas,id',
            'frequencias.*.situacao'     => 'nullable|in:presente,falta,justificada,atraso',
        ]);

        foreach ($request->input('frequencias', []) as $row) {
            $matriculaId = (int) $row['matricula_id'];
            $matricula   = \App\Models\Academico\Matricula::findOrFail($matriculaId);
            abort_unless((int) $matricula->turma_id === (int) $aula->turma_id, 403);

            $situacao = $row['situacao'] ?? null;
            if ($situacao === null || $situacao === '') {
                continue;
            }

            Frequencia::updateOrCreate(
                [
                    'matricula_id' => $matriculaId,
                    'aula_id'      => $aula->id,
                ],
                [
                    'aluno_id'  => $matricula->aluno_id,
                    'situacao'  => $situacao,
                    'observacao'=> $row['observacao'] ?? null,
                ]
            );
        }

        $aula->loadMissing('turma');

        return redirect()
            ->route('professor.frequencias.index', $aula->turma?->polivalente
                ? ['turma_id' => $aula->turma_id]
                : ['turma_id' => $aula->turma_id, 'disciplina_id' => $aula->disciplina_id]
            )
            ->with('success', 'Frequência salva.');
    }
}
