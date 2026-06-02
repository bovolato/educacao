<x-sigem-layout :title="'Notas — ' . $turma->nome">
    <x-page-header :title="$turma->nome" subtitle="Avaliações registradas"
        :back-route="route('escola.notas.index')" back-label="Turmas"/>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3 font-medium">Título</th>
                    <th class="px-5 py-3 font-medium">Disciplina</th>
                    <th class="px-5 py-3 font-medium">Data</th>
                    <th class="px-5 py-3 font-medium text-center">Notas lançadas</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($avaliacoes as $av)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $av->titulo }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $av->disciplina?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $av->data_avaliacao?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">{{ $av->notas_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('escola.notas.avaliacao', $av) }}" class="text-indigo-600 hover:underline font-medium">Ver notas</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Nenhuma avaliação para esta turma.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $avaliacoes->links() }}</div>
    </div>
</x-sigem-layout>
