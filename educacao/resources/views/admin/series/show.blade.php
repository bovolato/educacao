<x-sigem-layout :title="$serie->nome">
    <x-page-header :title="$serie->nome" :back-route="route('admin.series.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.series.edit', $serie) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Etapa de Ensino</p><p class="font-medium">{{ $serie->etapaEnsino->nome ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Sigla</p><p class="font-medium">{{ $serie->sigla ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Ordem</p><p class="font-medium">{{ $serie->ordem ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Status</p>
                <x-badge :color="$serie->ativo ? 'green' : 'gray'" dot>{{ $serie->ativo ? 'Ativa' : 'Inativa' }}</x-badge>
            </div>
        </div>
    </div>
</x-sigem-layout>
