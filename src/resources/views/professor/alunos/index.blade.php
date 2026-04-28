<x-sigem-layout title="Alunos">
    <x-page-header title="Alunos" subtitle="Veja a lista e abra o resumão do aluno (presenças, notas, tarefas e recados)"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas"/>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <x-professor-modulo-shell
        :vinculos="$vinculos"
        :turma-id="request('turma_id')"
        :disciplina-id="request('disciplina_id')"
        active="alunos"
        module-route="professor.alunos.index"
    >
        @if($matriculas && $matriculas->isEmpty())
            <x-empty-state
                title="Nenhum aluno encontrado"
                subtitle="Verifique se a turma tem matrículas ativas ou ajuste a busca."
            />
        @else
            <form method="GET" action="{{ route('professor.alunos.index') }}" class="mb-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <div class="w-full sm:max-w-md">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Buscar aluno</label>
                        <input name="busca" value="{{ request('busca') }}" placeholder="Digite o nome do aluno…"
                            class="w-full rounded-xl border-gray-300 text-sm" />
                    </div>
                    <div class="w-full sm:max-w-xs">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Turma (opcional)</label>
                        <select name="turma_id" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Todas</option>
                            @foreach(collect($vinculos)->groupBy('turma_id') as $grupoTurma)
                                @php $cab = $grupoTurma->first(); @endphp
                                <option value="{{ $cab->turma_id }}" @selected((string) request('turma_id') === (string) $cab->turma_id)>
                                    {{ $cab->turma_nome }}{{ $cab->escola_nome ? ' · '.$cab->escola_nome : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('professor.alunos.index') }}"
                            class="text-sm text-gray-600 hover:underline">Limpar</a>
                        <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Buscar</button>
                    </div>
                </div>
            </form>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-3 text-left">Aluno</th>
                            <th class="px-5 py-3 text-left">Turma</th>
                            <th class="px-5 py-3 text-left">Matrícula</th>
                            <th class="px-5 py-3 text-left">Situação</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($matriculas as $m)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $m->aluno?->nome ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">RA: {{ $m->aluno?->ra ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ $m->turma?->nome ?? '—' }}
                                    @if($m->turma?->escola?->nome)
                                        <div class="text-xs text-gray-500">{{ $m->turma->escola->nome }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-700">{{ $m->numero_matricula ?? $m->id }}</td>
                                <td class="px-5 py-3">
                                    <x-badge color="{{ $m->situacao === 'ativa' ? 'green' : 'gray' }}">{{ ucfirst($m->situacao) }}</x-badge>
                                </td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('professor.alunos.show', $m) }}"
                                        class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Abrir resumão</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">{{ $matriculas->links() }}</div>
            </div>
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>

