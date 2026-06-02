<x-sigem-layout title="Planos de aula">
    <x-page-header title="Planos de aula" subtitle="Planejamento por aula/data para cada turma"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas">
        <x-slot:actions>
            <x-action-button href="{{ route('professor.planos.create', array_filter(['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')])) }}">
                + Novo plano de aula
            </x-action-button>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <x-list-filters :fields="[
        ['type' => 'text', 'name' => 'busca', 'label' => 'Buscar', 'placeholder' => 'Título do plano…'],
        ['type' => 'select', 'name' => 'turma_id', 'label' => 'Turma', 'options' => $turmasOptions ?? []],
        ['type' => 'select', 'name' => 'disciplina_id', 'label' => 'Disciplina', 'options' => $disciplinasOptions ?? []],
        ['type' => 'date', 'name' => 'prevista_de', 'label' => 'Data prevista (de)'],
        ['type' => 'date', 'name' => 'prevista_ate', 'label' => 'Data prevista (até)'],
    ]" />

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3">Título</th>
                    <th class="px-5 py-3">Data prevista</th>
                    <th class="px-5 py-3">Turma</th>
                    <th class="px-5 py-3">Disciplina</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($planos as $p)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $p->titulo }}</td>
                        <td class="px-5 py-3">
                            @php $vencido = $p->data_prevista && $p->data_prevista->isPast(); @endphp
                            <div class="flex items-center gap-2">
                                <span>{{ $p->data_prevista?->format('d/m/Y') ?? '—' }}</span>
                                @if($vencido)
                                    <x-badge color="orange">Vencido</x-badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3">{{ $p->turma?->nome }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $p->disciplina?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('professor.planos.edit', $p) }}"
                                    class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Editar</a>
                                <form action="{{ route('professor.planos.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Remover?');">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Nenhum plano. Crie a partir de Minhas turmas.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $planos->links() }}</div>
    </div>
</x-sigem-layout>
