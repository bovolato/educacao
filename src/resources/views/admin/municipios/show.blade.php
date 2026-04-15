<x-sigem-layout :title="$municipio->nome">

    <x-page-header
        :title="$municipio->nome"
        subtitle="Detalhes do município"
        :back-route="route('admin.municipios.index')"
        back-label="Voltar para Municípios"
    >
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.municipios.edit', $municipio) }}" variant="secondary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'>
                Editar
            </x-action-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados do Município</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['UF', $municipio->uf],
                        ['Código IBGE', $municipio->codigo_ibge],
                        ['CNPJ', $municipio->cnpj],
                        ['Telefone', $municipio->telefone],
                        ['E-mail', $municipio->email],
                    ] as [$label, $value])
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ $value ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($municipio->logradouro)
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Endereço</h3>
                    <p class="text-sm text-gray-700">
                        {{ $municipio->logradouro }}{{ $municipio->numero ? ', ' . $municipio->numero : '' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $municipio->bairro }} · {{ $municipio->cidade }}/{{ $municipio->uf }}
                        {{ $municipio->cep ? ' · CEP ' . $municipio->cep : '' }}
                    </p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Escolas do Município</h3>
                    <span class="text-sm text-gray-500">{{ $escolas->count() }} escola(s)</span>
                </div>
                @if($escolas->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhuma escola cadastrada neste município.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($escolas as $escola)
                            <div class="px-6 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $escola->nome }}</p>
                                    <p class="text-xs text-gray-500">{{ $escola->turmas_count }} turma(s)</p>
                                </div>
                                <a href="{{ route('admin.escolas.show', $escola) }}" class="text-xs text-indigo-600 hover:underline">Ver escola</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Status</h3>
                <x-badge :color="$municipio->ativo ? 'green' : 'gray'" dot>{{ $municipio->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Estatísticas</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Escolas</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $municipio->escolas_count }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-sigem-layout>
