<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\AlunoRequest;
use App\Models\Academico\{AnotacaoProfessor, Aula, Avaliacao, Frequencia, Nota};
use App\Models\Pessoas\{Aluno, Pessoa, PessoaContato, PessoaEndereco, Responsavel};
use App\Models\Institucional\Escola;
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    public function index(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $alunos = Aluno::query()
            ->with([
                'pessoa:id,nome,cpf',
                'matriculas' => function ($q) {
                    $q->with([
                        'turma:id,nome',
                        'escola:id,nome',
                    ]);
                },
            ])
            ->when($escopo->escolaIdObrigatorioParaUsuarioEscola($user) !== null, fn ($q) => $escopo->aplicarEscopoAlunos($q, $user))
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('pessoa', function ($q) use ($term) {
                    $q->where('nome', 'like', $term)
                        ->orWhere('cpf', 'like', $term);
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'ativo') {
                    $q->where('ativo', true);
                } elseif ($request->status === 'inativo') {
                    $q->where('ativo', false);
                }
            })
            ->when($request->filled('cidade'), fn ($q) => $q->where('cidade_vinculo', $request->cidade))
            ->when(! $request->filled('busca'), fn ($q) => $q->where('ativo', true))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $cidades = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        return view('pessoas.alunos.index', compact('alunos', 'cidades'));
    }

    public function create(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $cidades = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $cidadePadraoEscola = $escopo->escolaDoUsuario($user)?->cidade;

        return view('pessoas.alunos.create', compact('cidades', 'cidadePadraoEscola'));
    }

    public function store(AlunoRequest $request)
    {
        $escopo = app(EscopoAcesso::class);
        if ($esc = $escopo->escolaDoUsuario($request->user())) {
            $request->merge(['cidade_vinculo' => $esc->cidade]);
        }

        DB::transaction(function () use ($request) {
            $pessoa = Pessoa::create([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'nome_mae'        => $request->nome_mae,
                'nome_pai'        => $request->nome_pai,
                'naturalidade'    => $request->naturalidade,
                'naturalidade_uf' => $request->naturalidade_uf,
                'ativo'           => true,
            ]);

            if ($request->filled('telefone') || $request->filled('email_contato')) {
                if ($request->filled('telefone')) {
                    PessoaContato::create([
                        'pessoa_id' => $pessoa->id,
                        'tipo'      => 'celular',
                        'valor'     => $request->telefone,
                        'principal' => true,
                    ]);
                }
                if ($request->filled('email_contato')) {
                    PessoaContato::create([
                        'pessoa_id' => $pessoa->id,
                        'tipo'      => 'email',
                        'valor'     => $request->email_contato,
                        'principal' => false,
                    ]);
                }
            }

            if ($request->filled('logradouro')) {
                PessoaEndereco::create([
                    'pessoa_id'  => $pessoa->id,
                    'logradouro' => $request->logradouro,
                    'numero'     => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro'     => $request->bairro,
                    'cidade'     => $request->cidade,
                    'uf'         => $request->uf,
                    'cep'        => $request->cep,
                    'principal'  => true,
                ]);
            }

            $aluno = Aluno::create([
                'pessoa_id'             => $pessoa->id,
                'cidade_vinculo'        => $request->cidade_vinculo,
                'ra'                    => $request->ra,
                'codigo_aluno'          => $request->codigo_aluno,
                'nis'                   => $request->nis,
                'necessidades_especiais' => $request->boolean('necessidades_especiais'),
                'descricao_necessidades' => $request->descricao_necessidades,
                'usa_transporte'        => $request->boolean('usa_transporte'),
                'ativo'                 => true,
            ]);

            // Responsável (simples): nome + telefone
            if ($request->filled('responsavel_nome')) {
                $pessoaResp = Pessoa::create([
                    'nome'  => $request->responsavel_nome,
                    'ativo' => true,
                ]);

                if ($request->filled('responsavel_telefone')) {
                    PessoaContato::create([
                        'pessoa_id' => $pessoaResp->id,
                        'tipo'      => 'celular',
                        'valor'     => $request->responsavel_telefone,
                        'principal' => true,
                    ]);
                }

                $resp = Responsavel::create([
                    'pessoa_id' => $pessoaResp->id,
                ]);

                $aluno->responsaveis()->attach($resp->id, [
                    'responsavel_principal' => true,
                    'recebe_boletim'        => true,
                ]);
            }
        });

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function show(Request $request, Aluno $aluno)
    {
        $this->authorize('view', $aluno);

        $aluno->loadMissing([
            'pessoa.contatos',
            'pessoa.enderecos',
            'responsaveis.pessoa',
            'matriculas.turma.serie',
            'matriculas.turma.turno',
            'matriculas.escola',
        ]);

        $periodos = collect(['1B','2B','3B','4B']);
        $periodoSelecionado = $request->input('periodo', '1B');

        $matriculasAtivas = $aluno->matriculas
            ->where('situacao', 'ativa')
            ->sortByDesc('data_matricula')
            ->values();

        $matriculaSelecionada = null;
        if ($request->filled('matricula_id')) {
            $matriculaSelecionada = $aluno->matriculas->firstWhere('id', (int) $request->matricula_id);
        }
        if (! $matriculaSelecionada) {
            $matriculaSelecionada = $matriculasAtivas->first() ?? $aluno->matriculas->sortByDesc('data_matricula')->first();
        }

        $freqResumo = null;
        $avaliacoes = collect();
        $notasPorAvaliacao = collect();
        $anotacoesProfessor = collect();

        if ($matriculaSelecionada && $matriculaSelecionada->turma_id) {
            $freqResumo = Frequencia::query()
                ->join('aulas', 'frequencias.aula_id', '=', 'aulas.id')
                ->where('frequencias.matricula_id', $matriculaSelecionada->id)
                ->where('aulas.turma_id', $matriculaSelecionada->turma_id)
                ->where('aulas.periodo', $periodoSelecionado)
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='presente' THEN 1 ELSE 0 END) as presentes")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='falta' THEN 1 ELSE 0 END) as faltas")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='justificada' THEN 1 ELSE 0 END) as justificadas")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='atraso' THEN 1 ELSE 0 END) as atrasos")
                ->first();

            $avaliacoes = Avaliacao::query()
                ->where('turma_id', $matriculaSelecionada->turma_id)
                ->where('periodo', $periodoSelecionado)
                ->with(['disciplina', 'professor.pessoa'])
                ->orderByDesc('data_avaliacao')
                ->orderByDesc('id')
                ->limit(50)
                ->get();

            $notasPorAvaliacao = Nota::query()
                ->whereIn('avaliacao_id', $avaliacoes->pluck('id'))
                ->where('matricula_id', $matriculaSelecionada->id)
                ->get()
                ->keyBy('avaliacao_id');

            $anotacoesProfessor = AnotacaoProfessor::query()
                ->where('matricula_id', $matriculaSelecionada->id)
                ->where('periodo', $periodoSelecionado)
                ->with('professor.pessoa')
                ->orderByDesc('id')
                ->limit(30)
                ->get();
        }

        return view('pessoas.alunos.show', compact(
            'aluno',
            'periodos',
            'periodoSelecionado',
            'matriculasAtivas',
            'matriculaSelecionada',
            'freqResumo',
            'avaliacoes',
            'notasPorAvaliacao',
            'anotacoesProfessor',
        ));
    }

    public function edit(Aluno $aluno)
    {
        $this->authorize('update', $aluno);

        $aluno->loadMissing(['pessoa.contatos', 'pessoa.enderecos', 'responsaveis.pessoa.contatos']);
        if (! $aluno->pessoa) {
            return redirect()
                ->route('pessoas.alunos.index')
                ->with('error', 'Este aluno não possui cadastro de pessoa vinculado. Corrija os dados no banco ou cadastre novamente.');
        }

        $escopo = app(EscopoAcesso::class);
        $user   = request()->user();

        $cidades = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $cidadePadraoEscola = $escopo->escolaDoUsuario($user)?->cidade;

        return view('pessoas.alunos.edit', compact('aluno', 'cidades', 'cidadePadraoEscola'));
    }

    public function update(AlunoRequest $request, Aluno $aluno)
    {
        $this->authorize('update', $aluno);

        $escopo = app(EscopoAcesso::class);
        if ($esc = $escopo->escolaDoUsuario($request->user())) {
            $request->merge(['cidade_vinculo' => $esc->cidade]);
        }

        $aluno->loadMissing(['pessoa', 'responsaveis.pessoa.contatos']);
        if (! $aluno->pessoa) {
            return redirect()
                ->route('pessoas.alunos.index')
                ->with('error', 'Não é possível atualizar: cadastro de pessoa ausente para este aluno.');
        }

        DB::transaction(function () use ($request, $aluno) {
            $aluno->pessoa->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'nome_mae'        => $request->nome_mae,
                'nome_pai'        => $request->nome_pai,
                'naturalidade'    => $request->naturalidade,
                'naturalidade_uf' => $request->naturalidade_uf,
            ]);

            $aluno->update([
                'cidade_vinculo'        => $request->cidade_vinculo,
                'ra'                    => $request->ra,
                'codigo_aluno'          => $request->codigo_aluno,
                'nis'                   => $request->nis,
                'necessidades_especiais' => $request->boolean('necessidades_especiais'),
                'descricao_necessidades' => $request->descricao_necessidades,
                'usa_transporte'        => $request->boolean('usa_transporte'),
                'ativo'                 => $request->boolean('ativo', true),
            ]);

            if ($request->filled('telefone')) {
                $aluno->pessoa->contatos()->updateOrCreate(
                    ['tipo' => 'celular'],
                    ['valor' => $request->telefone, 'principal' => true]
                );
            }
            if ($request->filled('email_contato')) {
                $aluno->pessoa->contatos()->updateOrCreate(
                    ['tipo' => 'email'],
                    ['valor' => $request->email_contato]
                );
            }

            if ($request->filled('logradouro')) {
                $aluno->pessoa->enderecos()->updateOrCreate(
                    ['principal' => true],
                    [
                        'logradouro' => $request->logradouro,
                        'numero'     => $request->numero,
                        'complemento' => $request->complemento,
                        'bairro'     => $request->bairro,
                        'cidade'     => $request->cidade,
                        'uf'         => $request->uf,
                        'cep'        => $request->cep,
                    ]
                );
            }

            // Responsável (simples): atualiza o principal; se não existir e vier nome, cria.
            if ($request->filled('responsavel_nome')) {
                $respPrincipal = $aluno->responsaveis()->wherePivot('responsavel_principal', true)->first()
                    ?? $aluno->responsaveis()->first();

                if (! $respPrincipal) {
                    $pessoaResp = Pessoa::create([
                        'nome'  => $request->responsavel_nome,
                        'ativo' => true,
                    ]);
                    $respPrincipal = Responsavel::create(['pessoa_id' => $pessoaResp->id]);
                    $aluno->responsaveis()->attach($respPrincipal->id, [
                        'responsavel_principal' => true,
                        'recebe_boletim'        => true,
                    ]);
                } else {
                    $respPrincipal->loadMissing('pessoa.contatos');
                    $respPrincipal->pessoa?->update(['nome' => $request->responsavel_nome]);
                }

                if ($request->filled('responsavel_telefone') && $respPrincipal?->pessoa) {
                    $respPrincipal->pessoa->contatos()->updateOrCreate(
                        ['tipo' => 'celular'],
                        ['valor' => $request->responsavel_telefone, 'principal' => true]
                    );
                }
            }
        });

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno atualizado com sucesso!');
    }

    public function destroy(Aluno $aluno)
    {
        $this->authorize('delete', $aluno);

        if ($aluno->matriculas()->where('situacao', 'ativa')->exists()) {
            return back()->with('error', 'Não é possível excluir um aluno com matrícula ativa.');
        }
        $aluno->update(['ativo' => false]);

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno inativado.');
    }
}
