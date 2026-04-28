<x-sigem-layout title="Avaliações">
    <x-page-header title="Avaliações" subtitle="Cadastre provas e atividades; depois lance notas na aba Notas"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas">
        <x-slot name="actions">
            @if(request('turma_id') && request('disciplina_id'))
                <a href="{{ route('professor.avaliacoes.create', ['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nova avaliação
                </a>
            @endif
        </x-slot>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @if($vinculos->isEmpty())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Você ainda não tem turmas/disciplinas vinculadas. Peça ao gestor da escola para concluir o vínculo.
        </div>
    @endif

    <x-professor-modulo-shell
        :vinculos="$vinculos"
        :turma-id="request('turma_id')"
        :disciplina-id="request('disciplina_id')"
        active="avaliacoes"
        module-route="professor.avaliacoes.index"
    >
        @php $contextoOk = request()->filled('turma_id') && request()->filled('disciplina_id'); @endphp

        @if($contextoOk && $avaliacoes && $avaliacoes->count() > 0)
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3">Título</th>
                            <th class="px-5 py-3">Data</th>
                            <th class="px-5 py-3">Período</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($avaliacoes as $av)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $av->titulo }}</td>
                                <td class="px-5 py-3">{{ $av->data_avaliacao?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3">{{ $av->periodo ?? '—' }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('professor.notas.lancar', $av) }}"
                                            class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Notas</a>
                                        <a href="{{ route('professor.avaliacoes.edit', $av) }}"
                                            class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Editar</a>
                                        <form action="{{ route('professor.avaliacoes.destroy', $av) }}" method="POST" class="inline" onsubmit="return confirm('Excluir esta avaliação?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">{{ $avaliacoes->links() }}</div>
            </div>
        @elseif($contextoOk && $avaliacoes && $avaliacoes->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-gray-700 font-medium mb-1">Nenhuma avaliação cadastrada</p>
                <p class="text-sm text-gray-500 mb-5">Use o botão <strong>Nova avaliação</strong> no topo ou o link abaixo.</p>
                <a href="{{ route('professor.avaliacoes.create', ['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')]) }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    Criar primeira avaliação
                </a>
            </div>
        @elseif(! $contextoOk)
            <x-empty-state
                title="Selecione o contexto"
                subtitle="Escolha a turma e disciplina acima para cadastrar e gerenciar avaliações."
            />
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>
