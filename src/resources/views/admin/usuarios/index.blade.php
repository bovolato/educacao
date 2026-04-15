<x-sigem-layout title="Usuários">
    <x-page-header title="Usuários" subtitle="Gestão de usuários e perfis de acesso">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.usuarios.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Novo Usuário
            </x-action-button>
        </x-slot>
    </x-page-header>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar nome ou e-mail..."
            class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <select name="perfil" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todos os perfis</option>
            @foreach($perfis as $p)
                <option value="{{ $p->name }}" @selected(request('perfil') === $p->name)>{{ $p->name }}</option>
            @endforeach
        </select>
        <select name="ativo" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todos</option>
            <option value="1" @selected(request('ativo') === '1')>Ativos</option>
            <option value="0" @selected(request('ativo') === '0')>Inativos</option>
        </select>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">Filtrar</button>
        @if(request()->hasAny(['busca', 'perfil', 'ativo']))
            <a href="{{ route('admin.usuarios.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">Limpar</a>
        @endif
    </form>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Usuário</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Perfil</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Escola</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Último Acesso</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @forelse($usuarios as $usuario)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm shrink-0">
                            {{ mb_substr($usuario->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $usuario->name }}</p>
                            <p class="text-xs text-gray-500">{{ $usuario->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    @foreach($usuario->roles as $role)
                        <x-badge color="indigo">{{ $role->name }}</x-badge>
                    @endforeach
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-600">{{ $usuario->escola->nome ?? '—' }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-500">{{ $usuario->ultimo_login_em?->diffForHumans() ?? '—' }}</td>
                <td class="px-5 py-3.5">
                    <x-badge :color="$usuario->ativo ? 'green' : 'gray'" dot>{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.usuarios.show', $usuario) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                        @if($usuario->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" onsubmit="return confirm('Excluir este usuário?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">Nenhum usuário encontrado.</td></tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $usuarios->total() }} registro(s)</span>
                {{ $usuarios->links() }}
            </div>
        </x-slot>
    </x-data-table>
</x-sigem-layout>
