<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\User;
use App\Models\Institucional\{Municipio, Escola};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::with(['roles', 'municipio', 'escola'])
            ->when($request->filled('busca'), fn($q) => $q->where('nome', 'like', '%' . $request->busca . '%')
                ->orWhere('email', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('perfil'), fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->perfil)))
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo', $request->tipo))
            ->when($request->filled('ativo'), fn($q) => $q->where('ativo', $request->ativo === '1'))
            ->orderBy('nome')->paginate(10)->withQueryString();

        $perfis = Role::orderBy('name')->get();

        return view('admin.usuarios.index', compact('usuarios', 'perfis'));
    }

    public function create()
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        $escolas    = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $perfis     = Role::orderBy('name')->get();
        return view('admin.usuarios.create', compact('municipios', 'escolas', 'perfis'));
    }

    public function store(UsuarioRequest $request)
    {
        $usuario = User::create([
            'tipo'         => $request->tipo,
            'municipio_id' => $request->municipio_id,
            'escola_id'    => $request->escola_id,
            'nome'         => $request->nome,
            'email'        => $request->email,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'ativo'        => $request->boolean('ativo', true),
        ]);

        if ($request->filled('perfil')) {
            $usuario->syncRoles([$request->perfil]);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function show(User $usuario)
    {
        $usuario->load(['roles', 'municipio', 'escola']);
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        $escolas    = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $perfis     = Role::orderBy('name')->get();
        return view('admin.usuarios.edit', compact('usuario', 'municipios', 'escolas', 'perfis'));
    }

    public function update(UsuarioRequest $request, User $usuario)
    {
        $dados = [
            'tipo'         => $request->tipo,
            'municipio_id' => $request->municipio_id,
            'escola_id'    => $request->escola_id,
            'nome'         => $request->nome,
            'email'        => $request->email,
            'username'     => $request->username,
            'ativo'        => $request->boolean('ativo', true),
        ];

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->password);
        }

        $usuario->update($dados);

        if ($request->filled('perfil')) {
            $usuario->syncRoles([$request->perfil]);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário atualizado!');
    }

    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'Você não pode excluir o próprio usuário.');
        }
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário removido!');
    }
}
