<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\ProfessorRequest;
use App\Models\Pessoas\Professor;
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
                'usuario:id,nome',
                'escola:id,nome,cidade',
            ])
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('usuario', fn ($q) => $q->where('nome', 'like', $term));
            })
            ->when($request->filled('escola'), fn ($q) => $q->where('escola_id', $request->escola))
            ->when(
                $request->filled('cidade') && ! $request->filled('escola'),
                fn ($q) => $q->whereHas('escola', fn ($e) => $e->where('cidade', $request->cidade))
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
            $usuario = User::create([
                'tipo'            => 'professor',
                'municipio_id'    => $escola->municipio_id,
                'escola_id'       => $escola->id,
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

            $usuario->assignRole('professor');

            Professor::create([
                'user_id'               => $usuario->id,
                'escola_id'             => $request->escola_id,
                'municipio_id'          => $escola->municipio_id,
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

        $professor->loadMissing(['usuario.contatos', 'usuario.roles', 'escola', 'municipio', 'turmas.serie', 'turmas.turno']);
        $usuarioVinculado = $professor->usuario;

        return view('pessoas.professores.show', compact('professor', 'usuarioVinculado'));
    }

    public function edit(Professor $professor)
    {
        $this->authorize('update', $professor);

        $professor->loadMissing(['usuario.contatos']);
        if (! $professor->usuario) {
            return redirect()
                ->route('pessoas.professores.index')
                ->with('error', 'Este professor não possui cadastro de usuário vinculado. Corrija os dados no banco ou cadastre novamente.');
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

        $professor->loadMissing('usuario');
        if (! $professor->usuario) {
            return redirect()
                ->route('pessoas.professores.index')
                ->with('error', 'Não é possível atualizar: cadastro de usuário ausente para este professor.');
        }

        $escola = Escola::findOrFail($request->escola_id);

        DB::transaction(function () use ($request, $professor, $escola) {
            $professor->usuario->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'escola_id'       => $request->escola_id,
                'municipio_id'    => $escola->municipio_id,
            ]);

            $professor->update([
                'escola_id'             => $request->escola_id,
                'municipio_id'          => $escola->municipio_id,
                'matricula_funcional'   => $request->matricula_funcional,
                'formacao'              => $request->formacao,
                'registro_profissional' => $request->registro_profissional,
                'data_admissao'         => $request->data_admissao,
                'ativo'                 => $request->boolean('ativo', true),
            ]);

            if ($request->filled('telefone')) {
                $professor->usuario->contatos()->updateOrCreate(['tipo' => 'celular'], ['valor' => $request->telefone, 'principal' => true]);
            }
            if ($request->filled('email_contato')) {
                $professor->usuario->contatos()->updateOrCreate(['tipo' => 'email'], ['valor' => $request->email_contato]);
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

        $professor->load(['usuario', 'escola', 'turmas']);

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

        $professor->loadMissing(['usuario.contatos', 'usuario.roles', 'escola.municipio']);

        $usuarioVinculado = $professor->usuario;

        $emailPrefill = $usuarioVinculado?->email
            ?? $professor->usuario?->contatos
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

        $professor->loadMissing(['usuario', 'escola']);

        $usuario = $professor->usuario;
        if (! $usuario) {
            return redirect()
                ->route('pessoas.professores.show', $professor)
                ->with('error', 'Não é possível criar login: professor sem usuário vinculado.');
        }

        if ($usuario->password) {
            return redirect()
                ->route('pessoas.professores.show', $professor)
                ->with('success', 'Este professor já possui credenciais de acesso.');
        }

        $request->validate([
            'email'    => ['required', 'email:rfc,dns', 'max:255', "unique:users,email,{$usuario->id}"],
            'username' => ['required', 'string', 'min:3', 'max:60', "unique:users,username,{$usuario->id}"],
            'password' => 'required|string|min:8|max:255',
            'ativo'    => 'nullable|boolean',
        ]);

        $usuario->update([
            'municipio_id' => $usuario->municipio_id ?? $professor->escola?->municipio_id,
            'escola_id'    => $usuario->escola_id ?? $professor->escola_id,
            'email'        => $request->email,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'ativo'        => $request->boolean('ativo', true),
        ]);

        // Garante o perfil professor no login
        $usuario->syncRoles(['professor']);

        return redirect()
            ->route('pessoas.professores.show', $professor)
            ->with('success', 'Login do professor criado com sucesso!');
    }
}
