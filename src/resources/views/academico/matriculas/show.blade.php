<x-sigem-layout :title="'Matrícula: ' . $matricula->aluno->pessoa->nome">
    <x-page-header :title="$matricula->aluno->pessoa->nome" subtitle="Ficha de Matrícula" :back-route="route('academico.matriculas.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('academico.matriculas.edit', $matricula) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados da Matrícula</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Nº Matrícula</p><p class="font-mono font-medium">{{ $matricula->numero_matricula ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Data</p><p class="font-medium">{{ $matricula->data_matricula?->format('d/m/Y') }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Escola</p><p class="font-medium">{{ $matricula->escola->nome ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Ano Letivo</p><p class="font-medium">{{ $matricula->anoLetivo->descricao ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Turma</p><p class="font-medium">{{ $matricula->turma->nome ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Situação</p>
                        @php $colors = ['ativa' => 'green', 'transferida' => 'yellow', 'evadida' => 'red', 'concluida' => 'blue', 'cancelada' => 'gray']; @endphp
                        <x-badge :color="$colors[$matricula->situacao] ?? 'gray'" dot>{{ ucfirst($matricula->situacao) }}</x-badge>
                    </div>
                </div>
            </div>

            {{-- Histórico --}}
            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Histórico de Movimentações</h3>
                </div>
                @if($matricula->historicos->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum histórico registrado.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($matricula->historicos->sortByDesc('data_movimentacao') as $hist)
                            <div class="px-6 py-3">
                                <div class="flex items-center justify-between mb-1">
                                    <x-badge color="indigo">{{ ucfirst($hist->tipo_movimentacao) }}</x-badge>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($hist->data_movimentacao)->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $hist->descricao }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 h-fit">
            <h3 class="font-semibold text-gray-800 mb-4">Dados do Aluno</h3>
            <div class="space-y-3">
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">Nome</p><p class="font-medium text-sm">{{ $matricula->aluno->pessoa->nome }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">RA</p><p class="font-mono text-sm">{{ $matricula->aluno->ra ?? '—' }}</p></div>
                <a href="{{ route('pessoas.alunos.show', $matricula->aluno) }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:underline mt-2">
                    Ver ficha completa do aluno →
                </a>
            </div>
        </div>
    </div>
</x-sigem-layout>
