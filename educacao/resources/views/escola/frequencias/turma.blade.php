<x-sigem-layout :title="'Frequência — ' . $turma->nome">
    <x-page-header :title="$turma->nome" subtitle="Aulas e resumo de frequência"
        :back-route="route('escola.frequencias.index')" back-label="Turmas"/>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3 font-medium">Data</th>
                    <th class="px-5 py-3 font-medium">Disciplina</th>
                    <th class="px-5 py-3 font-medium">Professor</th>
                    <th class="px-5 py-3 font-medium text-center">Presenças</th>
                    <th class="px-5 py-3 font-medium text-center">Faltas</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($aulas as $aula)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3 text-gray-900">{{ $aula->data_aula?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $aula->disciplina?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $aula->professor?->usuario?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-center text-green-700">{{ $aula->presencas_count }}</td>
                        <td class="px-5 py-3 text-center text-red-600">{{ $aula->faltas_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('escola.frequencias.aula', $aula) }}" class="text-indigo-600 hover:underline font-medium">Ver diário</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Nenhuma aula registrada para esta turma.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $aulas->links() }}</div>
    </div>
</x-sigem-layout>
