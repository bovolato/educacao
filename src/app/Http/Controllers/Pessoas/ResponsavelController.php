<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\ResponsavelRequest;
use App\Models\Pessoas\{Responsavel, Pessoa, PessoaContato};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponsavelController extends Controller
{
    public function index(Request $request)
    {
        $responsaveis = Responsavel::query()
            ->with(['pessoa:id,nome,cpf'])
            ->withCount('alunos')
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('pessoa', fn ($q) => $q->where('nome', 'like', $term));
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pessoas.responsaveis.index', compact('responsaveis'));
    }

    public function create()
    {
        return view('pessoas.responsaveis.create');
    }

    public function store(ResponsavelRequest $request)
    {
        DB::transaction(function () use ($request) {
            $pessoa = Pessoa::create([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'ativo'           => true,
            ]);

            if ($request->filled('telefone')) {
                PessoaContato::create(['pessoa_id' => $pessoa->id, 'tipo' => 'celular', 'valor' => $request->telefone, 'principal' => true]);
            }
            if ($request->filled('email_contato')) {
                PessoaContato::create(['pessoa_id' => $pessoa->id, 'tipo' => 'email', 'valor' => $request->email_contato, 'principal' => false]);
            }

            Responsavel::create([
                'pessoa_id'          => $pessoa->id,
                'tipo_responsavel'   => $request->tipo_responsavel,
                'responsavel_legal'  => $request->boolean('responsavel_legal', true),
                'financeiro'         => $request->boolean('financeiro'),
                'recebe_notificacao' => $request->boolean('recebe_notificacao', true),
            ]);
        });

        return redirect()->route('pessoas.responsaveis.index')->with('success', 'Responsável cadastrado com sucesso!');
    }

    public function show(Responsavel $responsavel)
    {
        $responsavel->loadMissing(['pessoa.contatos', 'alunos.pessoa']);
        return view('pessoas.responsaveis.show', compact('responsavel'));
    }

    public function edit(Responsavel $responsavel)
    {
        $responsavel->loadMissing(['pessoa.contatos']);
        if (! $responsavel->pessoa) {
            return redirect()
                ->route('pessoas.responsaveis.index')
                ->with('error', 'Este responsável não possui cadastro de pessoa vinculado. Corrija os dados no banco ou cadastre novamente.');
        }
        return view('pessoas.responsaveis.edit', compact('responsavel'));
    }

    public function update(ResponsavelRequest $request, Responsavel $responsavel)
    {
        $responsavel->loadMissing('pessoa');
        if (! $responsavel->pessoa) {
            return redirect()
                ->route('pessoas.responsaveis.index')
                ->with('error', 'Não é possível atualizar: cadastro de pessoa ausente para este responsável.');
        }

        DB::transaction(function () use ($request, $responsavel) {
            $responsavel->pessoa->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
            ]);

            $responsavel->update([
                'tipo_responsavel'   => $request->tipo_responsavel,
                'responsavel_legal'  => $request->boolean('responsavel_legal', true),
                'financeiro'         => $request->boolean('financeiro'),
                'recebe_notificacao' => $request->boolean('recebe_notificacao', true),
            ]);

            if ($request->filled('telefone')) {
                $responsavel->pessoa->contatos()->updateOrCreate(['tipo' => 'celular'], ['valor' => $request->telefone, 'principal' => true]);
            }
        });

        return redirect()->route('pessoas.responsaveis.index')->with('success', 'Responsável atualizado!');
    }

    public function destroy(Responsavel $responsavel)
    {
        $responsavel->delete();
        return redirect()->route('pessoas.responsaveis.index')->with('success', 'Responsável removido.');
    }
}
