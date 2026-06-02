<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academico\MatriculaRequest;
use App\Models\Academico\{Matricula, Turma, HistoricoMatricula};
use App\Models\Pessoas\Aluno;
use App\Models\Institucional\{Escola, AnoLetivo, Municipio};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatriculaController extends Controller
{
    public function index(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $matriculas = Matricula::with(['aluno.usuario', 'turma.serie', 'escola', 'anoLetivo'])
            ->when($escopo->escolaIdObrigatorioParaUsuarioEscola($user) !== null, fn ($q) => $escopo->aplicarEscopoMatriculas($q, $user))
            ->when($request->filled('busca'), fn($q) => $q->whereHas('aluno.usuario', fn($p) => $p->where('nome', 'like', '%' . $request->busca . '%')))
            ->when($request->filled('situacao'), fn($q) => $q->where('situacao', $request->situacao))
            ->when($request->filled('turma'), fn($q) => $q->where('turma_id', $request->turma))
            ->when($request->filled('escola'), fn($q) => $q->where('escola_id', $request->escola))
            ->when(
                $request->filled('cidade') && ! $request->filled('escola'),
                fn($q) => $q->whereHas('escola', fn($e) => $e->where('cidade', $request->cidade))
            )
            ->orderByDesc('data_matricula')
            ->paginate(10)->withQueryString();

        $cidades = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $escolasJson = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome', 'cidade'])
            ->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'cidade' => $e->cidade])
            ->values();

        $turmasQuery = Turma::query()
            ->where('status', 'ativa')
            ->with(['serie:id,nome', 'escola:id,cidade']);
        if ($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user)) {
            $turmasQuery->where('escola_id', $eid);
        }
        $turmasJson = $turmasQuery
            ->orderBy('nome')
            ->get()
            ->map(fn ($t) => [
                'id'         => (int) $t->id,
                'nome'       => $t->nome,
                'serie_nome' => $t->serie?->nome,
                'escola_id'  => (int) $t->escola_id,
                'cidade'     => $t->escola?->cidade,
            ])
            ->values();

        return view('academico.matriculas.index', compact('matriculas', 'cidades', 'escolasJson', 'turmasJson'));
    }

    public function create(Request $request)
    {
        $escopo             = app(EscopoAcesso::class);
        $user               = $request->user();
        $escolaMatriculaFixa = $escopo->escolaDoUsuario($user);

        $anos = AnoLetivo::where('ativo', true)->get();

        $municipioFixo = $escolaMatriculaFixa?->municipio_id;
        $municipios = Municipio::query()
            ->where('ativo', true)
            ->when($municipioFixo, fn ($q) => $q->where('id', $municipioFixo))
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);

        $escolasJson = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome', 'municipio_id'])
            ->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'municipio_id' => (int) $e->municipio_id])
            ->values();

        $alunosQuery = Aluno::query()->with('usuario')->where('ativo', true);
        if ($escopo->escolaIdObrigatorioParaUsuarioEscola($user) !== null) {
            $escopo->aplicarEscopoAlunos($alunosQuery, $user);
        }
        $alunosJson = $alunosQuery
            ->get()
            ->sortBy(fn ($a) => $a->nome)
            ->values()
            ->map(fn ($a) => [
                'id'           => (int) $a->id,
                'nome'         => $a->nome,
                'ra'           => $a->ra,
                'municipio_id' => (int) $a->municipio_id,
            ]);

        $alunoPreSelecionado = $request->filled('aluno_id') ? $request->aluno_id : null;

        return view('academico.matriculas.create', compact(
            'anos',
            'municipios',
            'escolasJson',
            'alunosJson',
            'alunoPreSelecionado',
            'escolaMatriculaFixa'
        ));
    }

    public function store(MatriculaRequest $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        if ($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user)) {
            $request->merge(['escola_id' => $eid]);
        }

        if (! $escopo->matriculaPayloadCoerenteComEscolaDoUsuario(
            $user,
            (int) $request->escola_id,
            (int) $request->turma_id
        )) {
            return back()->withErrors(['escola_id' => 'Turma ou escola inválida para o seu perfil.'])->withInput();
        }

        $aluno = Aluno::findOrFail($request->aluno_id);
        if (! $escopo->alunoAcessivelPeloUsuario($user, $aluno)) {
            abort(403);
        }

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
        $this->authorize('view', $matricula);

        $matricula->load([
            'aluno.usuario', 'turma.serie', 'turma.turno', 'escola', 'anoLetivo',
            'historicos',
        ]);

        return view('academico.matriculas.show', compact('matricula'));
    }

    public function edit(Matricula $matricula)
    {
        $this->authorize('update', $matricula);

        $turmas = Turma::where('escola_id', $matricula->escola_id)
            ->where('status', 'ativa')
            ->with(['serie', 'turno'])
            ->orderBy('nome')
            ->get();

        return view('academico.matriculas.edit', compact('matricula', 'turmas'));
    }

    public function update(MatriculaRequest $request, Matricula $matricula)
    {
        $this->authorize('update', $matricula);

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
        $this->authorize('delete', $matricula);

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
