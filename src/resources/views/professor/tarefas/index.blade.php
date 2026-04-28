<x-sigem-layout title="Tarefas">
    <x-page-header title="Tarefas" subtitle="Cadastre atividades e acompanhe por turma/disciplina"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas">
        <x-slot:actions>
            <x-action-button href="{{ route('professor.tarefas.create', array_filter(['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')])) }}">
                + Nova tarefa
            </x-action-button>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <x-list-filters :fields="[
        ['type' => 'text', 'name' => 'busca', 'label' => 'Buscar', 'placeholder' => 'Título da tarefa…'],
        ['type' => 'select', 'name' => 'turma_id', 'label' => 'Turma', 'options' => $turmasOptions ?? []],
        ['type' => 'select', 'name' => 'disciplina_id', 'label' => 'Disciplina', 'options' => $disciplinasOptions ?? []],
        ['type' => 'date', 'name' => 'entrega_de', 'label' => 'Entrega (de)'],
        ['type' => 'date', 'name' => 'entrega_ate', 'label' => 'Entrega (até)'],
    ]" />

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3">Título</th>
                    <th class="px-5 py-3">Entrega</th>
                    <th class="px-5 py-3">Turma</th>
                    <th class="px-5 py-3">Disciplina</th>
                    <th class="px-5 py-3">Valor</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tarefas as $t)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $t->titulo }}</td>
                        <td class="px-5 py-3">
                            @php $atrasada = $t->data_entrega && $t->data_entrega->isPast(); @endphp
                            <div class="flex items-center gap-2">
                                <span>{{ $t->data_entrega?->format('d/m/Y') ?? '—' }}</span>
                                @if($atrasada)
                                    <x-badge color="orange">Atrasada</x-badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3">{{ $t->turma?->nome }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $t->disciplina?->nome ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $t->valor ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('professor.tarefas.edit', $t) }}"
                                    class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Editar</a>
                                <form action="{{ route('professor.tarefas.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Remover?');">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Nenhuma tarefa.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $tarefas->links() }}</div>
    </div>
</x-sigem-layout>
