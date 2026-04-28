<x-sigem-layout title="Documentos — Escola">
    <x-page-header title="Documentos emitidos" subtitle="Registro de declarações e atestados"
        :back-route="route('dashboard')" back-label="Dashboard">
        <x-action-button href="{{ route('escola.documentos.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>'>Novo documento</x-action-button>
    </x-page-header>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3 font-medium">Emitido em</th>
                    <th class="px-5 py-3 font-medium">Tipo</th>
                    <th class="px-5 py-3 font-medium">Aluno</th>
                    <th class="px-5 py-3 font-medium">Turma</th>
                    <th class="px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documentos as $d)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3 text-gray-700">{{ $d->emitido_em?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-900">{{ $d->tipo_documento }}</td>
                        <td class="px-5 py-3">{{ $d->aluno?->pessoa?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $d->matricula?->turma?->nome ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('escola.documentos.imprimir', $d) }}" class="text-indigo-600 hover:underline font-medium">Abrir / imprimir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Nenhum documento emitido ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">{{ $documentos->links() }}</div>
    </div>
</x-sigem-layout>
