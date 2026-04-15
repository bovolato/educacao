<x-sigem-layout :title="$escola->nome">

    <x-page-header
        :title="$escola->nome"
        subtitle="Detalhes da unidade escolar"
        :back-route="route('admin.escolas.index')"
        back-label="Voltar para Escolas"
    >
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.escolas.edit', $escola) }}" variant="secondary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'>
                Editar
            </x-action-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados da Escola</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Município</p>
                        <a href="{{ route('admin.municipios.show', $escola->municipio) }}" class="text-sm font-medium text-indigo-600 hover:underline">
                            {{ $escola->municipio->nome }}/{{ $escola->municipio->uf }}
                        </a>
                    </div>
                    @foreach([
                        ['Código', $escola->codigo],
                        ['INEP', $escola->inep],
                        ['CNPJ', $escola->cnpj],
                        ['Telefone', $escola->telefone],
                        ['E-mail', $escola->email],
                        ['Diretor', $escola->diretor_nome],
                    ] as [$label, $value])
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ $value ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Endereço</h3>
                <p class="text-sm text-gray-700">
                    {{ $escola->logradouro }}{{ $escola->numero ? ', ' . $escola->numero : '' }}
                    {{ $escola->complemento ? ' — ' . $escola->complemento : '' }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $escola->bairro }} · {{ $escola->cidade }}/{{ $escola->uf }}
                    {{ $escola->cep ? ' · CEP ' . $escola->cep : '' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Turmas Ativas</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500">{{ $turmas->total() }} turma(s)</span>
                        <a href="{{ route('academico.turmas.create') }}?escola_id={{ $escola->id }}" class="text-xs text-indigo-600 hover:underline font-medium">+ Nova Turma</a>
                    </div>
                </div>
                @if($turmas->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhuma turma cadastrada.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($turmas as $turma)
                            <div class="px-6 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $turma->nome }}</p>
                                    <p class="text-xs text-gray-500">{{ $turma->serie->nome ?? '—' }} · {{ $turma->turno->nome ?? '—' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600">{{ $turma->matriculas_ativas_count }} alunos</span>
                                    <a href="{{ route('academico.turmas.show', $turma) }}" class="text-xs text-indigo-600 hover:underline">Ver</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-3 border-t border-gray-100">
                        {{ $turmas->links() }}
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Professores</h3>
                    <span class="text-sm text-gray-500">{{ $professores->total() }} professor(es)</span>
                </div>
                @if($professores->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum professor vinculado.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($professores as $professor)
                            <div class="px-6 py-3 flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-800">{{ $professor->pessoa->nome }}</p>
                                <a href="{{ route('pessoas.professores.show', $professor) }}" class="text-xs text-indigo-600 hover:underline">Ver</a>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-3 border-t border-gray-100">
                        {{ $professores->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Status</h3>
                @php
                    $statusMap = ['ativa' => ['green', 'Ativa'], 'inativa' => ['red', 'Inativa'], 'em_obras' => ['yellow', 'Em Obras']];
                    [$color, $label] = $statusMap[$escola->status] ?? ['gray', $escola->status];
                @endphp
                <x-badge :color="$color" dot>{{ $label }}</x-badge>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Estatísticas</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Turmas ativas</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $turmas->total() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Alunos matriculados</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $matriculas }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Professores</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $professores->total() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Salas</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $escola->salas->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Salas</h3>
                    <a href="{{ route('admin.escolas.salas.index', $escola) }}" class="text-xs text-indigo-600 hover:underline font-medium">Gerenciar</a>
                </div>
                @if($escola->salas->isEmpty())
                    <div class="px-5 py-6 text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-sm text-gray-500 mb-3">Nenhuma sala cadastrada</p>
                        <a href="{{ route('admin.escolas.salas.create', $escola) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-medium hover:bg-indigo-700 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Cadastrar Sala
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($escola->salas->take(5) as $sala)
                            <div class="px-5 py-2.5 flex justify-between items-center">
                                <p class="text-sm text-gray-700">{{ $sala->nome }}</p>
                                <x-badge :color="$sala->ativo ? 'green' : 'gray'">{{ $sala->ativo ? 'Ativa' : 'Inativa' }}</x-badge>
                            </div>
                        @endforeach
                        @if($escola->salas->count() > 5)
                            <div class="px-5 py-2.5 text-center">
                                <a href="{{ route('admin.escolas.salas.index', $escola) }}" class="text-xs text-indigo-600 hover:underline">Ver todas ({{ $escola->salas->count() }})</a>
                            </div>
                        @endif
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100">
                        <a href="{{ route('admin.escolas.salas.create', $escola) }}" class="text-xs text-indigo-600 hover:underline font-medium">+ Nova Sala</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-sigem-layout>
