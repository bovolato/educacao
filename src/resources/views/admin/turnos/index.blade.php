<x-sigem-layout title="Turnos">
    <x-page-header title="Turnos" subtitle="Turnos escolares disponíveis">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.turnos.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Novo Turno
            </x-action-button>
        </x-slot>
    </x-page-header>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Turno</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Horário</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @forelse($turnos as $turno)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3.5 font-medium text-gray-800">{{ $turno->nome }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-600">
                    @if($turno->hora_inicio && $turno->hora_fim)
                        {{ $turno->hora_inicio }} às {{ $turno->hora_fim }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <x-badge :color="$turno->ativo ? 'green' : 'gray'" dot>{{ $turno->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.turnos.show', $turno) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('admin.turnos.edit', $turno) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                        <form method="POST" action="{{ route('admin.turnos.destroy', $turno) }}" onsubmit="return confirm('Excluir este turno?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">Nenhum turno encontrado.</td></tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $turnos->total() }} registro(s)</span>
                {{ $turnos->links() }}
            </div>
        </x-slot>
    </x-data-table>
</x-sigem-layout>
