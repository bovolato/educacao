<x-sigem-layout :title="$turno->nome">
    <x-page-header :title="$turno->nome" :back-route="route('admin.turnos.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.turnos.edit', $turno) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Hora Início</p><p class="font-medium">{{ $turno->hora_inicio ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Hora Fim</p><p class="font-medium">{{ $turno->hora_fim ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Status</p>
                <x-badge :color="$turno->ativo ? 'green' : 'gray'" dot>{{ $turno->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
            </div>
        </div>
    </div>
</x-sigem-layout>
