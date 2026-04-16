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

        <div class="bg-white rounded-2xl border border-gray-200 lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Disciplinas da Turma</h3>
                <span class="text-sm text-gray-500">{{ $disciplinasTurma->total() }} disciplina(s)</span>
            </div>
            @if($disciplinasTurma->isEmpty())
                <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhuma disciplina vinculada.</p>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($disciplinasTurma as $disc)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $disc->nome }}</p>
                                <p class="text-xs text-gray-500">{{ $disc->sigla ?? '' }} {{ $disc->pivot->carga_horaria ? '· ' . $disc->pivot->carga_horaria . 'h' : '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $disciplinasTurma->links() }}
                </div>
            @endif
        </div>
    </div>

</x-sigem-layout>
