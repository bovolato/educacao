<x-sigem-layout title="Anotações">
    <x-page-header title="Anotações" subtitle="Registros do professor sobre alunos (por bimestre)"
        :back-route="route('dashboard')" back-label="Dashboard">
        <x-slot name="actions">
            <x-action-button href="{{ route('professor.anotacoes.create') }}">+ Nova anotação</x-action-button>
        </x-slot>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <x-list-filters :fields="[
        ['type' => 'text', 'name' => 'busca', 'label' => 'Buscar', 'placeholder' => 'Assunto ou texto…'],
        ['type' => 'select', 'name' => 'turma_id', 'label' => 'Turma', 'options' => $turmasOptions ?? []],
    ]" />

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3">Aluno</th>
                    <th class="px-5 py-3">Turma</th>
                    <th class="px-5 py-3">Assunto</th>
                    <th class="px-5 py-3">Criada</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($anotacoes as $a)
                    @php $nome = $a->matricula?->aluno?->pessoa?->nome ?? '—'; @endphp
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $nome }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $a->turma?->nome ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $a->assunto }}</td>
                        <td class="px-5 py-3">{{ $a->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <x-action-button href="{{ route('professor.anotacoes.show', $a) }}" size="sm">Ver</x-action-button>
                                <x-action-button href="{{ route('professor.anotacoes.edit', $a) }}" variant="secondary" size="sm">Editar</x-action-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Nenhuma anotação neste bimestre.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $anotacoes->links() }}</div>
    </div>
</x-sigem-layout>

