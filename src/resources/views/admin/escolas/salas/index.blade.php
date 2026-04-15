<x-sigem-layout :title="'Salas — ' . $escola->nome">

    <x-page-header :title="'Salas de ' . $escola->nome" subtitle="Gerenciar salas da escola"
        :back-route="route('admin.escolas.show', $escola)" back-label="Voltar para Escola">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.escolas.salas.create', $escola) }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Nova Sala
            </x-action-button>
        </x-slot>
    </x-page-header>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Sala</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Código</th>
            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Capacidade</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Tipo</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @forelse($salas as $sala)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <p class="font-medium text-gray-800">{{ $sala->nome }}</p>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $sala->codigo ?? '—' }}</td>
                <td class="px-5 py-3.5 text-center text-sm text-gray-700">{{ $sala->capacidade ?? '—' }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $sala->tipo ?? '—' }}</td>
                <td class="px-5 py-3.5">
                    <x-badge :color="$sala->ativo ? 'green' : 'gray'" dot>{{ $sala->ativo ? 'Ativa' : 'Inativa' }}</x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.escolas.salas.edit', $sala) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.escolas.salas.destroy', $sala) }}" onsubmit="return confirm('Confirma a exclusão desta sala?')">
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
                <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                    Nenhuma sala cadastrada nesta escola.
                </td>
            </tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $salas->total() }} sala(s)</span>
                {{ $salas->links() }}
            </div>
        </x-slot>
    </x-data-table>

</x-sigem-layout>
