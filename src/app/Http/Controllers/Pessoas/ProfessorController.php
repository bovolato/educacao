<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\ProfessorRequest;
use App\Models\Pessoas\{Professor, Pessoa, PessoaContato};
use App\Models\User;
use App\Models\Institucional\Escola;
use App\Models\Academico\{Turma, Disciplina};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $professores = Professor::query()
            ->when($escopo->escolaIdObrigatorioParaUsuarioEscola($user) !== null, fn ($q) => $escopo->aplicarEscopoProfessores($q, $user))
            ->with([
                'pessoa:id,nome',
                'escola:id,nome,cidade',
            ])
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('pessoa', fn ($q) => $q->where('nome', 'like', $term));
            })
            ->when($request->filled('escola'), fn ($q) => $q->where('escola_id', $request->escola))
            ->when(
                $request->filled('cidade') && ! $request->filled('escola'),
                function ($q) use ($request) {
                    $c = $request->cidade;
                    $q->where(function ($q2) use ($c) {
                        $q2->where('cidade_vinculo', $c)
                            ->orWhereHas('escola', fn ($e) => $e->where('cidade', $c));
                    });
                }
            )
            ->when($request->filled('ativo'), fn ($q) => $q->where('ativo', $request->ativo === '1'))
            ->when(! $request->filled('busca') && ! $request->filled('ativo'), fn ($q) => $q->where('ativo', true))
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

        $escolasJson = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome', 'cidade'])
            ->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'cidade' => $e->cidade])
            ->values();

        return view('pessoas.professores.index', compact('professores', 'cidades', 'escolasJson'));
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

        $escolasJson = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome', 'cidade'])
            ->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'cidade' => $e->cidade])
            ->values();

        $escolaFixa = $escopo->escolaDoUsuario($user);

        return view('pessoas.professores.create', compact('cidades', 'escolasJson', 'escolaFixa'));
    }

    public function store(ProfessorRequest $request)
    {
        $escopo = app(EscopoAcesso::class);
        if ($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($request->user())) {
            $request->merge(['escola_id' => $eid]);
        }

        $escola = Escola::findOrFail($request->escola_id);

        DB::transaction(function () use ($request, $escola) {
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

            Professor::create([
                'pessoa_id'             => $pessoa->id,
                'escola_id'             => $request->escola_id,
                'cidade_vinculo'        => $escola->cidade,
                'matricula_funcional'   => $request->matricula_funcional,
                'formacao'              => $request->formacao,
                'registro_profissional' => $request->registro_profissional,
                'data_admissao'         => $request->data_admissao,
                'ativo'                 => true,
            ]);
        });

        return redirect()->route('pessoas.professores.index')->with('success', 'Professor cadastrado com sucesso!');
    }

    public function show(Professor $professor)
    {
        $this->authorize('view', $professor);

        $professor->loadMissing(['pessoa.contatos', 'escola', 'turmas.serie', 'turmas.turno']);
        $usuarioVinculado = $professor->pessoa_id
            ? User::query()->where('pessoa_id', $professor->pessoa_id)->with('roles')->first()
            : null;

        return view('pessoas.professores.show', compact('professor', 'usuarioVinculado'));
    }

    public function edit(Professor $professor)
    {
        $this->authorize('update', $professor);

        $professor->loadMissing(['pessoa.contatos']);
        if (! $professor->pessoa) {
            return redirect()
                ->route('pessoas.professores.index')
                ->with('error', 'Este professor não possui cadastro de pessoa vinculado. Corrija os dados no banco ou cadastre novamente.');
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

        $escolasJson = Escola::query()
            ->when($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($user), fn ($q) => $q->where('id', $eid))
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome', 'cidade'])
            ->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'cidade' => $e->cidade])
            ->values();

        $escolaFixa = $escopo->escolaDoUsuario($user);

        return view('pessoas.professores.edit', compact('professor', 'cidades', 'escolasJson', 'escolaFixa'));
    }

    public function update(ProfessorRequest $request, Professor $professor)
    {
        $this->authorize('update', $professor);

        $escopo = app(EscopoAcesso::class);
        if ($eid = $escopo->escolaIdObrigatorioParaUsuarioEscola($request->user())) {
            $request->merge(['escola_id' => $eid]);
        }

        $professor->loadMissing('pessoa');
        if (! $professor->pessoa) {
            return redirect()
                ->route('pessoas.professores.index')
                ->with('error', 'Não é possível atualizar: cadastro de pessoa ausente para este professor.');
        }

        $escola = Escola::findOrFail($request->escola_id);

        DB::transaction(function () use ($request, $professor, $escola) {
            $professor->pessoa->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
            ]);

            $professor->update([
                'escola_id'             => $request->escola_id,
                'cidade_vinculo'        => $escola->cidade,
                'matricula_funcional'   => $request->matricula_funcional,
                'formacao'              => $request->formacao,
                'registro_profissional' => $request->registro_profissional,
                'data_admissao'         => $request->data_admissao,
                'ativo'                 => $request->boolean('ativo', true),
            ]);

            if ($request->filled('telefone')) {
                $professor->pessoa->contatos()->updateOrCreate(['tipo' => 'celular'], ['valor' => $request->telefone, 'principal' => true]);
            }
            if ($request->filled('email_contato')) {
                $professor->pessoa->contatos()->updateOrCreate(['tipo' => 'email'], ['valor' => $request->email_contato]);
            }
        });

        return redirect()->route('pessoas.professores.index')->with('success', 'Professor atualizado!');
    }

    public function destroy(Professor $professor)
    {
        $this->authorize('delete', $professor);

        $professor->update(['ativo' => false]);
        return redirect()->route('pessoas.professores.index')->with('success', 'Professor inativado.');
    }

    public function vincularTurmas(Professor $professor)
    {
        $this->authorize('vincularTurmas', $professor);

        $professor->load(['pessoa', 'escola', 'turmas']);

        $turmas = Turma::where('escola_id', $professor->escola_id)
            ->where('status', 'ativa')
            ->with(['serie', 'turno'])
            ->orderBy('nome')
            ->get();

        $disciplinas = Disciplina::where('ativo', true)->orderBy('nome')->get();

        $vinculosExistentes = DB::table('turma_professores')
            ->where('professor_id', $professor->id)
            ->get(['turma_id', 'disciplina_id', 'titular']);

        return view('pessoas.professores.vincular-turmas', compact('professor', 'turmas', 'disciplinas', 'vinculosExistentes'));
    }

    public function salvarVinculoTurmas(Request $request, Professor $professor)
    {
        $this->authorize('vincularTurmas', $professor);

        $request->validate([
            'vinculos'               => 'nullable|array',
            'vinculos.*.turma_id'    => 'required|exists:turmas,id',
            'vinculos.*.disciplina_id' => 'required|exists:disciplinas,id',
            'vinculos.*.titular'     => 'boolean',
        ]);

        DB::table('turma_professores')->where('professor_id', $professor->id)->delete();

        if ($request->filled('vinculos')) {
            foreach ($request->vinculos as $vinculo) {
                DB::table('turma_professores')->insert([
                    'turma_id'      => $vinculo['turma_id'],
                    'disciplina_id' => $vinculo['disciplina_id'],
                    'professor_id'  => $professor->id,
                    'titular'       => $vinculo['titular'] ?? true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        return redirect()->route('pessoas.professores.show', $professor)
            ->with('success', 'Vínculos de turmas atualizados com sucesso!');
    }

    public function usuarioForm(Professor $professor)
    {
        $this->authorize('update', $professor);

        $professor->loadMissing(['pessoa.contatos', 'escola.municipio']);

        $usuarioVinculado = $professor->pessoa_id
            ? User::query()->where('pessoa_id', $professor->pessoa_id)->with('roles')->first()
            : null;

        $emailPrefill = $professor->pessoa?->contatos
            ?->firstWhere('tipo', 'email')
            ?->valor;

        $usernamePrefill = null;
        if ($emailPrefill && str_contains($emailPrefill, '@')) {
            $usernamePrefill = explode('@', $emailPrefill)[0] ?: null;
        }

        return view('pessoas.professores.usuario', compact('professor', 'usuarioVinculado', 'emailPrefill', 'usernamePrefill'));
    }

    public function usuarioStore(Request $request, Professor $professor)
    {
        $this->authorize('update', $professor);

        $professor->loadMissing(['pessoa', 'escola']);

        if (! $professor->pessoa_id || ! $professor->pessoa) {
            return redirect()
                ->route('pessoas.professores.show', $professor)
                ->with('error', 'Não é possível criar login: professor sem pessoa vinculada.');
        }

        $jaExiste = User::query()->where('pessoa_id', $professor->pessoa_id)->first();
        if ($jaExiste) {
            return redirect()
                ->route('pessoas.professores.show', $professor)
                ->with('success', 'Este professor já possui um usuário vinculado.');
        }

        $request->validate([
            'email'    => 'required|email:rfc,dns|max:255|unique:users,email',
            'username' => 'required|string|min:3|max:60|unique:users,username',
            'password' => 'required|string|min:8|max:255',
            'ativo'    => 'nullable|boolean',
        ]);

        $usuario = User::create([
            'pessoa_id'    => $professor->pessoa_id,
            'municipio_id' => $professor->escola?->municipio_id,
            'escola_id'    => $professor->escola_id,
            'name'         => $professor->pessoa->nome,
            'email'        => $request->email,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'ativo'        => $request->boolean('ativo', true),
        ]);

        // Garante o perfil professor no login criado
        $usuario->syncRoles(['professor']);

        return redirect()
            ->route('pessoas.professores.show', $professor)
            ->with('success', 'Login do professor criado e vinculado com sucesso!');
    }
}
