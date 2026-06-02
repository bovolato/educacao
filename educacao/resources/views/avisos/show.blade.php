<x-sigem-layout :title="$aviso->titulo">
    <x-page-header :title="$aviso->titulo" :back-route="route('avisos.index')" back-label="Voltar para Avisos">
        <x-slot name="actions">
            <x-action-button href="{{ route('avisos.edit', $aviso) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    @php $tipoColors = ['geral' => 'blue', 'escola' => 'green', 'turma' => 'purple']; @endphp
                    <x-badge :color="$tipoColors[$aviso->tipo_destino] ?? 'gray'">{{ ucfirst($aviso->tipo_destino) }}</x-badge>
                    @if(!$aviso->ativo) <x-badge color="gray">Inativo</x-badge> @endif
                </div>
                <div class="prose max-w-none text-sm text-gray-700 whitespace-pre-wrap">{{ $aviso->mensagem }}</div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Informações</h3>
                <div class="space-y-3">
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Publicado por</p><p class="text-sm font-medium">{{ $aviso->usuario->nome ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Data</p><p class="text-sm font-medium">{{ $aviso->publicado_em?->format('d/m/Y H:i') }}</p></div>
                    @if($aviso->escola)
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Escola</p><p class="text-sm font-medium">{{ $aviso->escola->nome }}</p></div>
                    @endif
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Município</p><p class="text-sm font-medium">{{ $aviso->municipio->nome ?? '—' }}</p></div>
                </div>
            </div>
        </div>
    </div>
</x-sigem-layout>
