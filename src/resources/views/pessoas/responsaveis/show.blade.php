<x-sigem-layout :title="$responsavel->pessoa->nome">
    <x-page-header :title="$responsavel->pessoa->nome" subtitle="Ficha do Responsável" :back-route="route('pessoas.responsaveis.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('pessoas.responsaveis.edit', $responsavel) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">CPF</p><p class="font-medium">{{ $responsavel->pessoa->cpf ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Tipo</p><p class="font-medium">{{ $responsavel->tipo_responsavel }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Responsável Legal</p><p class="font-medium">{{ $responsavel->responsavel_legal ? 'Sim' : 'Não' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Recebe Notificações</p><p class="font-medium">{{ $responsavel->recebe_notificacao ? 'Sim' : 'Não' }}</p></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Alunos Vinculados</h3>
                </div>
                @if($responsavel->alunos->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum aluno vinculado.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($responsavel->alunos as $aluno)
                            <div class="px-6 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $aluno->pessoa->nome }}</p>
                                    <p class="text-xs text-gray-500">RA: {{ $aluno->ra ?? '—' }}</p>
                                </div>
                                <a href="{{ route('pessoas.alunos.show', $aluno) }}" class="text-xs text-indigo-600 hover:underline">Ver ficha</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 h-fit">
            <h3 class="font-semibold text-gray-800 mb-3">Contatos</h3>
            <div class="space-y-2">
                @foreach($responsavel->pessoa->contatos as $contato)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">{{ $contato->tipo }}</p>
                        <p class="text-sm font-medium text-gray-700">{{ $contato->valor }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-sigem-layout>
