<x-sigem-layout title="Materiais">
    <x-page-header title="Materiais didáticos" subtitle="Links e arquivos de apoio por turma/disciplina"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas">
        <x-slot:actions>
            <x-action-button href="{{ route('professor.materiais.create', array_filter(['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')])) }}">
                + Novo material
            </x-action-button>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <x-list-filters :fields="[
        ['type' => 'text', 'name' => 'busca', 'label' => 'Buscar', 'placeholder' => 'Título do material…'],
        ['type' => 'select', 'name' => 'turma_id', 'label' => 'Turma', 'options' => $turmasOptions ?? []],
        ['type' => 'select', 'name' => 'disciplina_id', 'label' => 'Disciplina', 'options' => $disciplinasOptions ?? []],
        ['type' => 'select', 'name' => 'visivel_aluno', 'label' => 'Visível aluno', 'options' => ['1' => 'Sim', '0' => 'Não'], 'placeholder' => 'Todos'],
    ]" />

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3">Título</th>
                    <th class="px-5 py-3">Turma</th>
                    <th class="px-5 py-3">Disciplina</th>
                    <th class="px-5 py-3">Visível aluno</th>
                    <th class="px-5 py-3">Link</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($materiais as $m)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $m->titulo }}</td>
                        <td class="px-5 py-3">{{ $m->turma?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $m->disciplina?->nome ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $m->visivel_aluno ? 'Sim' : 'Não' }}</td>
                        <td class="px-5 py-3">
                            @if($m->link)
                                <a href="{{ $m->link }}" target="_blank" class="text-indigo-600 hover:underline">Abrir</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('professor.materiais.edit', $m) }}"
                                    class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Editar</a>
                                <form action="{{ route('professor.materiais.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Remover?');">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Nenhum material.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $materiais->links() }}</div>
    </div>
</x-sigem-layout>
