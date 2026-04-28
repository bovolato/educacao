<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\AlunoRequest;
use App\Models\Pessoas\{Aluno, Pessoa, PessoaContato, PessoaEndereco};
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

            Aluno::create([
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
        });

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function show(Aluno $aluno)
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

        return view('pessoas.alunos.show', compact('aluno'));
    }

    public function edit(Aluno $aluno)
    {
        $this->authorize('update', $aluno);

        $aluno->loadMissing(['pessoa.contatos', 'pessoa.enderecos']);
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

        $aluno->loadMissing('pessoa');
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
