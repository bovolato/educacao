<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Academico\{Avaliacao, Nota};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotasProfessorController extends Controller
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

            return redirect()->route('professor.notas.index', [
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
                ->get();
        }

        return view('professor.notas.index', compact('vinculos', 'avaliacoes'));
    }

    public function lancar(Avaliacao $avaliacao)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $avaliacao->professor_id === (int) $prof->id, 403);

        $avaliacao->load('turma');
        $matriculas = $avaliacao->turma->matriculasAtivas()
            ->with(['aluno.pessoa'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('matriculas.*')
            ->get();

        $notasPorMatricula = Nota::query()
            ->where('avaliacao_id', $avaliacao->id)
            ->get()
            ->keyBy('matricula_id');

        return view('professor.notas.lancar', compact('avaliacao', 'matriculas', 'notasPorMatricula'));
    }

    public function salvar(Request $request, Avaliacao $avaliacao)
    {
        $prof = auth()->user()->professor;
        abort_unless((int) $avaliacao->professor_id === (int) $prof->id, 403);

        $request->validate([
            'notas'                   => 'required|array',
            'notas.*.matricula_id'    => 'required|exists:matriculas,id',
            'notas.*.nota'           => 'nullable|numeric|min:0|max:1000',
            'notas.*.falta_na_avaliacao' => 'nullable|boolean',
            'notas.*.observacao'      => 'nullable|string|max:1000',
        ]);

        foreach ($request->input('notas', []) as $row) {
            $matriculaId = (int) $row['matricula_id'];
            $matricula   = \App\Models\Academico\Matricula::findOrFail($matriculaId);
            abort_unless((int) $matricula->turma_id === (int) $avaliacao->turma_id, 403);

            Nota::updateOrCreate(
                [
                    'avaliacao_id' => $avaliacao->id,
                    'aluno_id'     => $matricula->aluno_id,
                ],
                [
                    'matricula_id'       => $matriculaId,
                    'nota'               => $row['nota'] !== '' && $row['nota'] !== null ? $row['nota'] : null,
                    'falta_na_avaliacao' => ! empty($row['falta_na_avaliacao']),
                    'observacao'         => $row['observacao'] ?? null,
                ]
            );
        }

        return redirect()->route('professor.notas.index', [
            'turma_id' => $avaliacao->turma_id, 'disciplina_id' => $avaliacao->disciplina_id,
        ])->with('success', 'Notas salvas.');
    }
}
