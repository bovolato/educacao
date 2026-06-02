<x-sigem-layout title="Notas da avaliação">
    <x-page-header :title="$avaliacao->titulo" :subtitle="$avaliacao->turma->nome"
        :back-route="route('escola.notas.turma', $avaliacao->turma)" back-label="Voltar"/>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3 font-medium">Aluno</th>
                    <th class="px-5 py-3 font-medium text-right">Nota</th>
                    <th class="px-5 py-3 font-medium">Falta na avaliação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($notas as $n)
                    <tr>
                        <td class="px-5 py-3 text-gray-900">{{ $n->aluno?->usuario?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-mono">{{ $n->nota !== null ? number_format($n->nota, 1, ',', '.') : '—' }}</td>
                        <td class="px-5 py-3">{{ $n->falta_na_avaliacao ? 'Sim' : 'Não' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-gray-500">Nenhuma nota lançada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-sigem-layout>
