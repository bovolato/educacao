<x-sigem-layout title="Escolas">

    <x-page-header title="Gestão de Escolas" subtitle="Unidades escolares da rede municipal">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.escolas.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Nova Escola
            </x-action-button>
        </x-slot>
    </x-page-header>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="flex-1">
            <input type="text" name="busca" value="{{ request('busca') }}"
                placeholder="Buscar por nome, código ou INEP..."
                class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
        </div>
        <select name="cidade" class="min-w-[180px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as cidades</option>
            @foreach($cidades as $cidade)
                <option value="{{ $cidade }}" @selected(request('cidade') === $cidade)>{{ $cidade }}</option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todos os status</option>
            <option value="ativa" @selected(request('status') === 'ativa')>Ativa</option>
            <option value="inativa" @selected(request('status') === 'inativa')>Inativa</option>
            <option value="em_obras" @selected(request('status') === 'em_obras')>Em Obras</option>
        </select>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
            Filtrar
        </button>
        @if(request()->hasAny(['busca', 'cidade', 'status']))
            <a href="{{ route('admin.escolas.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                Limpar
            </a>
        @endif
    </form>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Escola</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Código / INEP</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Diretor</th>
            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Turmas</th>
            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Salas</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @forelse($escolas as $escola)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $escola->nome }}</p>
                            <p class="text-xs text-gray-500">{{ $escola->cidade }}/{{ $escola->uf }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <p class="text-sm text-gray-700">{{ $escola->codigo ?? '—' }}</p>
                    <p class="text-xs text-gray-500">INEP: {{ $escola->inep ?? '—' }}</p>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $escola->diretor_nome ?? '—' }}</td>
                <td class="px-5 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold">
                        {{ $escola->turmas_count }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <a href="{{ route('admin.escolas.salas.index', $escola) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $escola->salas_count > 0 ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700' }} text-sm font-semibold hover:ring-2 hover:ring-offset-1 hover:ring-violet-300 transition-all" title="Gerenciar Salas">
                        {{ $escola->salas_count }}
                    </a>
                </td>
                <td class="px-5 py-3.5">
                    @php
                        $statusMap = ['ativa' => ['green', 'Ativa'], 'inativa' => ['red', 'Inativa'], 'em_obras' => ['yellow', 'Em Obras']];
                        [$color, $label] = $statusMap[$escola->status] ?? ['gray', $escola->status];
                    @endphp
                    <x-badge :color="$color" dot>{{ $label }}</x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.escolas.salas.index', $escola) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-violet-50 hover:text-violet-600 transition-colors" title="Gerenciar Salas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </a>
                        <a href="{{ route('admin.escolas.show', $escola) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Visualizar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.escolas.edit', $escola) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.escolas.destroy', $escola) }}" onsubmit="return confirm('Confirma a exclusão desta escola?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    Nenhuma escola encontrada.
                </td>
            </tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $escolas->total() }} escola(s) encontrada(s)</span>
                {{ $escolas->links() }}
            </div>
        </x-slot>
    </x-data-table>

</x-sigem-layout>
