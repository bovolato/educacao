<x-sigem-layout title="Notas — Escola">
    <x-page-header title="Notas" subtitle="Visão por turma"
        :back-route="route('dashboard')" back-label="Dashboard"/>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3 font-medium">Turma</th>
                    <th class="px-5 py-3 font-medium">Série / Turno</th>
                    <th class="px-5 py-3 font-medium text-center">Avaliações</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($turmas as $turma)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $turma->nome }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $turma->serie?->nome ?? '—' }} · {{ $turma->turno?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-center text-gray-700">{{ $turma->avaliacoes_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('escola.notas.turma', $turma) }}" class="text-indigo-600 hover:underline font-medium">Detalhar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Nenhuma turma ativa nesta escola.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-sigem-layout>
