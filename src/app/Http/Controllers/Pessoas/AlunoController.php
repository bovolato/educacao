<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\AlunoRequest;
use App\Models\Pessoas\{Aluno, Pessoa, PessoaContato, PessoaEndereco};
use App\Models\Institucional\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    public function index(Request $request)
    {
        $alunos = Aluno::with(['pessoa', 'matriculas.turma', 'matriculas.escola'])
            ->whereHas('pessoa', function ($q) use ($request) {
                if ($request->filled('busca')) {
                    $q->where('nome', 'like', '%' . $request->busca . '%')
                      ->orWhere('cpf', 'like', '%' . $request->busca . '%');
                }
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'ativo') {
                    $q->where('ativo', true);
                } elseif ($request->status === 'inativo') {
                    $q->where('ativo', false);
                }
            })
            ->when(!$request->filled('busca'), fn($q) => $q->where('ativo', true))
            ->orderBy('id', 'desc')
            ->paginate(20)->withQueryString();

        return view('pessoas.alunos.index', compact('alunos'));
    }

    public function create()
    {
        return view('pessoas.alunos.create');
    }

    public function store(AlunoRequest $request)
    {
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
        $aluno->load([
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
        $aluno->load(['pessoa.contatos', 'pessoa.enderecos']);
        return view('pessoas.alunos.edit', compact('aluno'));
    }

    public function update(AlunoRequest $request, Aluno $aluno)
    {
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
                'ra'                    => $request->ra,
                'codigo_aluno'          => $request->codigo_aluno,
                'nis'                   => $request->nis,
                'necessidades_especiais' => $request->boolean('necessidades_especiais'),
                'descricao_necessidades' => $request->descricao_necessidades,
                'usa_transporte'        => $request->boolean('usa_transporte'),
                'ativo'                 => $request->boolean('ativo', true),
            ]);

            // Atualizar contato principal
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

            // Atualizar endereço principal
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
        if ($aluno->matriculas()->where('situacao', 'ativa')->exists()) {
            return back()->with('error', 'Não é possível excluir um aluno com matrícula ativa.');
        }
        $aluno->update(['ativo' => false]);
        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno inativado.');
    }
}
