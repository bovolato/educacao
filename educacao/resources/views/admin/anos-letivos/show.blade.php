<x-sigem-layout :title="'Ano Letivo ' . $anoLetivo->descricao">
    <x-page-header :title="'Ano Letivo ' . $anoLetivo->descricao" :back-route="route('admin.anos-letivos.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.anos-letivos.edit', $anoLetivo) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Município</p><p class="font-medium">{{ $anoLetivo->municipio->nome ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Início</p><p class="font-medium">{{ $anoLetivo->data_inicio?->format('d/m/Y') }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Término</p><p class="font-medium">{{ $anoLetivo->data_fim?->format('d/m/Y') }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Status</p>
                <x-badge :color="$anoLetivo->ativo ? 'green' : 'gray'" dot>{{ $anoLetivo->ativo ? 'Ativo' : 'Encerrado' }}</x-badge>
            </div>
        </div>
    </div>
</x-sigem-layout>
