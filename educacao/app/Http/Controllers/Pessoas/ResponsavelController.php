<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\ResponsavelRequest;
use App\Models\Pessoas\Responsavel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponsavelController extends Controller
{
    public function index(Request $request)
    {
        $responsaveis = Responsavel::query()
            ->with(['usuario:id,nome,cpf'])
            ->withCount('alunos')
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('usuario', fn ($q) => $q->where('nome', 'like', $term));
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
            $usuario = User::create([
                'tipo'            => 'responsavel',
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'ativo'           => true,
            ]);

            if ($request->filled('telefone')) {
                $usuario->contatos()->create(['tipo' => 'celular', 'valor' => $request->telefone, 'principal' => true]);
            }
            if ($request->filled('email_contato')) {
                $usuario->contatos()->create(['tipo' => 'email', 'valor' => $request->email_contato, 'principal' => false]);
            }

            Responsavel::create([
                'user_id'            => $usuario->id,
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
        $responsavel->loadMissing(['usuario.contatos', 'alunos.usuario']);
        return view('pessoas.responsaveis.show', compact('responsavel'));
    }

    public function edit(Responsavel $responsavel)
    {
        $responsavel->loadMissing(['usuario.contatos']);
        if (! $responsavel->usuario) {
            return redirect()
                ->route('pessoas.responsaveis.index')
                ->with('error', 'Este responsável não possui cadastro de usuário vinculado. Corrija os dados no banco ou cadastre novamente.');
        }
        return view('pessoas.responsaveis.edit', compact('responsavel'));
    }

    public function update(ResponsavelRequest $request, Responsavel $responsavel)
    {
        $responsavel->loadMissing('usuario');
        if (! $responsavel->usuario) {
            return redirect()
                ->route('pessoas.responsaveis.index')
                ->with('error', 'Não é possível atualizar: cadastro de usuário ausente para este responsável.');
        }

        DB::transaction(function () use ($request, $responsavel) {
            $responsavel->usuario->update([
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
                $responsavel->usuario->contatos()->updateOrCreate(['tipo' => 'celular'], ['valor' => $request->telefone, 'principal' => true]);
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
