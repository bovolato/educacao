<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academico\MatriculaRequest;
use App\Models\Academico\{Matricula, Turma, HistoricoMatricula};
use App\Models\Pessoas\Aluno;
use App\Models\Institucional\{Escola, AnoLetivo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatriculaController extends Controller
{
    public function index(Request $request)
    {
        $matriculas = Matricula::with(['aluno.pessoa', 'turma.serie', 'escola', 'anoLetivo'])
            ->when($request->filled('busca'), fn($q) => $q->whereHas('aluno.pessoa', fn($p) => $p->where('nome', 'like', '%' . $request->busca . '%')))
            ->when($request->filled('situacao'), fn($q) => $q->where('situacao', $request->situacao))
            ->when($request->filled('turma'), fn($q) => $q->where('turma_id', $request->turma))
            ->when($request->filled('escola'), fn($q) => $q->where('escola_id', $request->escola))
            ->orderByDesc('data_matricula')
            ->paginate(10)->withQueryString();

        $turmas  = Turma::where('status', 'ativa')->with('serie')->orderBy('nome')->get();
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();

        return view('academico.matriculas.index', compact('matriculas', 'turmas', 'escolas'));
    }

    public function create(Request $request)
    {
        $alunos  = Aluno::with('pessoa')->where('ativo', true)->get()->sortBy(fn($a) => $a->pessoa->nome);
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $anos    = AnoLetivo::where('ativo', true)->get();

        $alunoPreSelecionado = $request->filled('aluno_id') ? $request->aluno_id : null;

        return view('academico.matriculas.create', compact('alunos', 'escolas', 'anos', 'alunoPreSelecionado'));
    }

    public function store(MatriculaRequest $request)
    {
        $existe = Matricula::where('aluno_id', $request->aluno_id)
            ->where('ano_letivo_id', $request->ano_letivo_id)
            ->where('situacao', 'ativa')
            ->exists();

        if ($existe) {
            return back()->withErrors(['aluno_id' => 'Este aluno já possui uma matrícula ativa para o ano letivo selecionado.'])->withInput();
        }

        $numeroMatricula = $request->numero_matricula;
        if (empty($numeroMatricula)) {
            $ano = date('Y');
            $ultimo = Matricula::where('numero_matricula', 'like', $ano . '%')
                ->orderByDesc('numero_matricula')
                ->value('numero_matricula');
            $seq = $ultimo ? ((int) substr($ultimo, 4)) + 1 : 1;
            $numeroMatricula = $ano . str_pad($seq, 6, '0', STR_PAD_LEFT);
        }

        DB::transaction(function () use ($request, $numeroMatricula) {
            $matricula = Matricula::create(array_merge($request->validated(), [
                'numero_matricula' => $numeroMatricula,
                'data_matricula'   => now()->toDateString(),
                'situacao'         => 'ativa',
                'criado_por'       => auth()->id(),
            ]));

            HistoricoMatricula::create([
                'matricula_id'      => $matricula->id,
                'tipo_movimentacao' => 'matricula',
                'data_movimentacao' => now(),
                'descricao'         => 'Matrícula realizada',
                'usuario_id'        => auth()->id(),
            ]);

            $aluno = Aluno::find($request->aluno_id);
            if ($aluno && !$aluno->ativo) {
                $aluno->update(['ativo' => true]);
            }
        });

        return redirect()->route('academico.matriculas.index')->with('success', 'Matrícula realizada com sucesso!');
    }

    public function show(Matricula $matricula)
    {
        $matricula->load([
            'aluno.pessoa', 'turma.serie', 'turma.turno', 'escola', 'anoLetivo',
            'historicos',
        ]);

        return view('academico.matriculas.show', compact('matricula'));
    }

    public function edit(Matricula $matricula)
    {
        $turmas = Turma::where('escola_id', $matricula->escola_id)
            ->where('status', 'ativa')
            ->with(['serie', 'turno'])
            ->orderBy('nome')
            ->get();

        return view('academico.matriculas.edit', compact('matricula', 'turmas'));
    }

    public function update(MatriculaRequest $request, Matricula $matricula)
    {
        $situacaoAnterior = $matricula->situacao;

        DB::transaction(function () use ($request, $matricula, $situacaoAnterior) {
            $matricula->update($request->validated());

            if ($situacaoAnterior !== $request->situacao) {
                HistoricoMatricula::create([
                    'matricula_id'      => $matricula->id,
                    'tipo_movimentacao' => $request->situacao,
                    'data_movimentacao' => now(),
                    'descricao'         => 'Situação alterada de ' . $situacaoAnterior . ' para ' . $request->situacao,
                    'usuario_id'        => auth()->id(),
                ]);

                if ($request->situacao !== 'ativa') {
                    $aluno = $matricula->aluno;
                    $outraAtiva = Matricula::where('aluno_id', $aluno->id)
                        ->where('id', '!=', $matricula->id)
                        ->where('situacao', 'ativa')
                        ->exists();
                    if (!$outraAtiva) {
                        $aluno->update(['ativo' => false]);
                    }
                }
            }
        });

        return redirect()->route('academico.matriculas.index')->with('success', 'Matrícula atualizada!');
    }

    public function destroy(Matricula $matricula)
    {
        DB::transaction(function () use ($matricula) {
            $matricula->update(['situacao' => 'cancelada']);

            HistoricoMatricula::create([
                'matricula_id'      => $matricula->id,
                'tipo_movimentacao' => 'cancelada',
                'data_movimentacao' => now(),
                'descricao'         => 'Matrícula cancelada',
                'usuario_id'        => auth()->id(),
            ]);

            $aluno = $matricula->aluno;
            $outraAtiva = Matricula::where('aluno_id', $aluno->id)
                ->where('id', '!=', $matricula->id)
                ->where('situacao', 'ativa')
                ->exists();
            if (!$outraAtiva) {
                $aluno->update(['ativo' => false]);
            }
        });

        return redirect()->route('academico.matriculas.index')->with('success', 'Matrícula cancelada.');
    }
}
