<x-sigem-layout title="Notas">
    <x-page-header title="Notas" subtitle="Escolha a avaliação e lance as notas dos alunos"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas"/>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
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
        active="notas"
        module-route="professor.notas.index"
    >
        @php $contextoOk = request()->filled('turma_id') && request()->filled('disciplina_id'); @endphp

        @if($contextoOk && $avaliacoes !== null && $avaliacoes->count() > 0)
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3">Avaliação</th>
                            <th class="px-5 py-3">Data</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($avaliacoes as $av)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $av->titulo }}</td>
                                <td class="px-5 py-3">{{ $av->data_avaliacao?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('professor.notas.lancar', $av) }}" class="text-indigo-600 hover:underline font-medium">Lançar notas</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-3">Não vê a avaliação desejada? Cadastre em <strong>Avaliações</strong> (aba acima) ou use o atalho <strong>+</strong> nas abas.</p>
        @elseif($contextoOk && $avaliacoes !== null && $avaliacoes->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-gray-700 font-medium mb-1">Nenhuma avaliação nesta turma/disciplina</p>
                <p class="text-sm text-gray-500 mb-5">Crie uma avaliação (prova, trabalho etc.) para depois lançar as notas aqui.</p>
                <a href="{{ route('professor.avaliacoes.create', ['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')]) }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    Criar primeira avaliação
                </a>
            </div>
        @elseif(! $contextoOk)
            <x-empty-state
                title="Selecione o contexto"
                subtitle="Escolha a turma e disciplina acima para ver as avaliações e lançar as notas."
            />
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>
