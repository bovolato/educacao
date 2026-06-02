<x-sigem-layout :title="$turma->nome">

    <x-page-header :title="$turma->nome" :subtitle="$turma->escola->nome" :back-route="route('academico.turmas.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('academico.turmas.edit', $turma) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @foreach([
            ['Série', $turma->serie->nome ?? '—', 'purple'],
            ['Turno', $turma->turno->nome ?? '—', 'blue'],
            ['Sala', $turma->sala->nome ?? '—', 'violet'],
            ['Capacidade', $turma->capacidade ?? '—', 'gray'],
            ['Alunos', $matriculasTurma->total(), 'green'],
        ] as [$label, $valor, $cor])
            <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-500 uppercase mb-1">{{ $label }}</p>
                <p class="text-lg font-bold text-gray-800">{{ $valor }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white rounded-2xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Alunos Matriculados</h3>
                <span class="text-sm text-gray-500">{{ $matriculasTurma->total() }} aluno(s)</span>
            </div>
            @if($matriculasTurma->isEmpty())
                <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum aluno matriculado.</p>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($matriculasTurma as $i => $matricula)
                        <div class="px-6 py-2.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400 w-5">{{ $matriculasTurma->firstItem() + $i }}.</span>
                                <p class="text-sm text-gray-800">{{ $matricula->aluno->nome }}</p>
                            </div>
                            <a href="{{ route('pessoas.alunos.show', $matricula->aluno) }}" class="text-xs text-indigo-600 hover:underline">ver</a>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $matriculasTurma->links() }}
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Professores e Disciplinas</h3>
            </div>
            @if($professoresTurma->isEmpty())
                <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum professor vinculado.</p>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($professoresTurma as $professor)
                        @php
                            $disciplina = $disciplinasPorId->get($professor->pivot->disciplina_id);
                        @endphp
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $professor->nome }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $disciplina?->nome ?? '—' }}
                                    @if($professor->pivot->titular) · <span class="text-indigo-600 font-medium">Titular</span> @endif
                                </p>
                            </div>
                            <a href="{{ route('pessoas.professores.show', $professor) }}" class="text-xs text-indigo-600 hover:underline">ver</a>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $professoresTurma->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="font-semibold text-gray-800">Painel pedagógico</h3>
                <p class="text-xs text-gray-500 mt-0.5">Aulas, frequência e avaliações registradas nesta turma (por bimestre).</p>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <label class="text-xs text-gray-500">Bimestre</label>
                <select name="periodo" class="rounded-xl border-gray-300 text-sm" onchange="this.form.submit()">
                    @foreach($periodos as $p)
                        <option value="{{ $p }}" @selected($p === $periodoSelecionado)>{{ $p }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="alunos_page" value="{{ request('alunos_page') }}">
                <input type="hidden" name="professores_page" value="{{ request('professores_page') }}">
            </form>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach([
                    ['Aulas', $aulas->total(), 'indigo'],
                    ['Matrículas ativas', $totalMatriculasAtivas, 'gray'],
                    ['Presentes', (int) ($freqResumoTurma?->presentes ?? 0), 'green'],
                    ['Faltas', (int) ($freqResumoTurma?->faltas ?? 0), 'red'],
                    ['Atrasos', (int) ($freqResumoTurma?->atrasos ?? 0), 'violet'],
                ] as [$label, $valor, $cor])
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase mb-1">{{ $label }}</p>
                        <p class="text-lg font-bold text-gray-800">{{ $valor }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-800">Aulas</h4>
                    <span class="text-xs text-gray-500">{{ $aulas->total() }} registro(s)</span>
                </div>
                @if($aulas->isEmpty())
                    <p class="px-5 py-8 text-center text-gray-500 text-sm">Nenhuma aula registrada neste bimestre.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-[900px] w-full text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th class="px-5 py-3">Data</th>
                                    <th class="px-5 py-3">Disciplina</th>
                                    <th class="px-5 py-3">Professor</th>
                                    <th class="px-5 py-3">Conteúdo</th>
                                    <th class="px-5 py-3">Frequência</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($aulas as $aula)
                                    @php
                                        $lancadas = (int) ($aula->frequencias_count ?? 0);
                                        $concluida = $totalMatriculasAtivas > 0 && $lancadas >= $totalMatriculasAtivas;
                                    @endphp
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-5 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $aula->data_aula?->format('d/m/Y') }}</td>
                                        <td class="px-5 py-3">{{ $aula->disciplina?->nome ?? '—' }}</td>
                                        <td class="px-5 py-3">{{ $aula->professor?->usuario?->nome ?? '—' }}</td>
                                        <td class="px-5 py-3">
                                            @if(($aula->conteudos_count ?? 0) > 0)
                                                <span class="text-emerald-700 font-medium">Preenchido</span>
                                            @else
                                                <span class="text-amber-700">Pendente</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-2">
                                                <x-badge color="{{ $concluida ? 'green' : ($lancadas > 0 ? 'yellow' : 'gray') }}">
                                                    {{ $concluida ? 'Concluída' : ($lancadas > 0 ? 'Em andamento' : 'Pendente') }}
                                                </x-badge>
                                                <span class="text-xs text-gray-500">{{ $lancadas }}/{{ $totalMatriculasAtivas }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100">{{ $aulas->links() }}</div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-800">Avaliações</h4>
                    <span class="text-xs text-gray-500">{{ $avaliacoes->count() }} registro(s)</span>
                </div>
                @if($avaliacoes->isEmpty())
                    <p class="px-5 py-8 text-center text-gray-500 text-sm">Nenhuma avaliação registrada neste bimestre.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-[900px] w-full text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th class="px-5 py-3">Data</th>
                                    <th class="px-5 py-3">Título</th>
                                    <th class="px-5 py-3">Disciplina</th>
                                    <th class="px-5 py-3">Professor</th>
                                    <th class="px-5 py-3">Notas lançadas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($avaliacoes as $av)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-5 py-3 whitespace-nowrap">{{ $av->data_avaliacao?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $av->titulo }}</td>
                                        <td class="px-5 py-3">{{ $av->disciplina?->nome ?? '—' }}</td>
                                        <td class="px-5 py-3">{{ $av->professor?->usuario?->nome ?? '—' }}</td>
                                        <td class="px-5 py-3">
                                            <span class="text-gray-700">{{ (int) ($av->notas_count ?? 0) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-sigem-layout>
