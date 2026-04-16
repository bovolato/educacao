<x-sigem-layout title="Responsáveis">
    <x-page-header title="Responsáveis" subtitle="Responsáveis vinculados aos alunos">
        <x-slot name="actions">
            <x-action-button href="{{ route('pessoas.responsaveis.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Novo Responsável
            </x-action-button>
        </x-slot>
    </x-page-header>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome..."
            class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">Filtrar</button>
        @if(request('busca'))
            <a href="{{ route('pessoas.responsaveis.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">Limpar</a>
        @endif
    </form>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Responsável</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Tipo</th>
            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Alunos</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @forelse($responsaveis as $resp)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3.5">
                    <p class="font-medium text-gray-800 text-sm">{{ $resp->nome }}</p>
                    <p class="text-xs text-gray-500">CPF: {{ $resp->pessoa?->cpf ?? '—' }}</p>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-600">{{ $resp->tipo_responsavel }}</td>
                <td class="px-5 py-3.5 text-center">
                    <span class="text-sm font-semibold text-gray-700">{{ $resp->alunos_count }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('pessoas.responsaveis.show', $resp) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('pessoas.responsaveis.edit', $resp) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">Nenhum responsável encontrado.</td></tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $responsaveis->total() }} registro(s)</span>
                {{ $responsaveis->links() }}
            </div>
        </x-slot>
    </x-data-table>
</x-sigem-layout>
