<x-sigem-layout title="Minhas turmas">
    <x-page-header title="Minhas turmas" subtitle="Cada card é uma turma. Suas disciplinas nesta turma aparecem abaixo — frequência e notas são por disciplina (cada registro no diário é separado)."
        :back-route="route('dashboard')" back-label="Dashboard"/>

    <div class="space-y-5">
        @forelse($turmasAgrupadas as $turmaId => $disciplinasDoTurma)
            @php
                $primeiro = $disciplinasDoTurma->first();
                $polivalente = (bool) ($primeiro->turma_polivalente ?? false);
            @endphp
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-gray-900 text-lg">{{ $primeiro->turma_nome }}</p>
                        @if($polivalente)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-xs font-medium">
                                Polivalente
                            </span>
                        @endif
                    </div>
                    @if($primeiro->escola_nome)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $primeiro->escola_nome }}</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-2">
                        {{ $disciplinasDoTurma->count() }} disciplina(s) nesta turma — escolha uma para lançar frequência, notas ou conteúdo.
                    </p>

                    @if($polivalente)
                        <div class="mt-3 flex flex-wrap gap-2 text-sm">
                            <a href="{{ route('professor.frequencias.index', ['turma_id' => $primeiro->turma_id]) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                Frequência (turma)
                            </a>
                            <a href="{{ route('professor.aulas.index', ['turma_id' => $primeiro->turma_id]) }}" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-800 hover:bg-teal-100">
                                Aulas / conteúdo (turma)
                            </a>
                            <span class="text-xs text-gray-500 self-center">Notas e avaliações continuam por disciplina.</span>
                        </div>
                    @endif
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($disciplinasDoTurma as $v)
                        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $v->disciplina_nome }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Diário da disciplina · turma {{ $v->turma_nome }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-sm shrink-0">
                                @if(! $polivalente)
                                    <a href="{{ route('professor.frequencias.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Frequência</a>
                                @endif
                                <a href="{{ route('professor.notas.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100">Notas</a>
                                <a href="{{ route('professor.avaliacoes.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-800 hover:bg-violet-100">Avaliações</a>
                                @if(! $polivalente)
                                    <a href="{{ route('professor.aulas.index', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-800 hover:bg-teal-100">Aulas</a>
                                @endif
                                <a href="{{ route('professor.planos-ensino.create', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">Plano ensino</a>
                                <a href="{{ route('professor.planos.create', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">Plano aula</a>
                                <a href="{{ route('professor.materiais.create', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">Material</a>
                                <a href="{{ route('professor.tarefas.create', ['turma_id' => $v->turma_id, 'disciplina_id' => $v->disciplina_id]) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">Tarefa</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-10">Nenhum vínculo com turmas. Peça ao gestor para vincular disciplinas.</p>
        @endforelse
    </div>
</x-sigem-layout>
