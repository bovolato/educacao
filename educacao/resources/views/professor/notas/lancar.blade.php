<x-sigem-layout title="Lançar notas">
    <x-page-header :title="$avaliacao->titulo" subtitle="Lançamento de notas"
        :back-route="route('professor.notas.index', ['turma_id' => $avaliacao->turma_id, 'disciplina_id' => $avaliacao->disciplina_id])" back-label="Voltar"/>

    <x-professor-context-bar
        :turma-nome="$avaliacao->turma?->nome"
        :disciplina-nome="$avaliacao->disciplina?->nome"
        :data-label="'Avaliação: ' . ($avaliacao->data_avaliacao?->format('d/m/Y') ?? '—') . ' · Valor: ' . ($avaliacao->valor ?? '—')"
    />

    @php
        $totalAlunos = $matriculas->count();
        $preenchidas = $notasPorMatricula->filter(fn ($n) => $n && $n->nota !== null)->count();
        $faltas = $notasPorMatricula->filter(fn ($n) => $n && (bool) $n->falta_na_avaliacao)->count();
    @endphp

    <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-700">
                <x-badge color="indigo">{{ $preenchidas }} / {{ $totalAlunos }} com nota</x-badge>
                <x-badge color="orange">{{ $faltas }} falta(s)</x-badge>
            </div>
            <div class="flex items-center gap-2">
                <input id="buscaAluno" type="text" placeholder="Buscar aluno…"
                    class="w-64 max-w-full rounded-xl border-gray-300 text-sm" />
                <button type="button" id="limparBusca" class="text-sm text-gray-600 hover:underline">Limpar</button>
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">Dica: você pode buscar pelo nome do aluno e usar o checkbox de falta quando ele não fez a prova.</p>
    </div>

    <form method="POST" action="{{ route('professor.notas.salvar', $avaliacao) }}">
        @csrf
        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <div class="bg-gray-50 px-5 py-3 text-left text-sm text-gray-600 min-w-[1100px]">
                    <div class="flex items-center gap-3">
                        <div class="w-64">Aluno</div>
                        <div class="w-32">Nota</div>
                        <div class="w-20 text-center">Falta</div>
                        <div class="flex-1">Observação</div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 min-w-[1100px]" id="tabelaNotas">
                    @foreach($matriculas as $i => $mat)
                        @php $n = $notasPorMatricula->get($mat->id); @endphp
                        <div class="flex items-center gap-3 px-5 py-4" data-aluno="{{ strtolower($mat->aluno?->usuario?->nome ?? '') }}">
                            <div class="w-64 font-medium text-gray-900 truncate">{{ $mat->aluno?->usuario?->nome ?? '—' }}</div>

                            <div class="w-32">
                                <input type="hidden" name="notas[{{ $i }}][matricula_id]" value="{{ $mat->id }}">
                                <input type="number" step="0.1" name="notas[{{ $i }}][nota]" value="{{ old('notas.'.$i.'.nota', $n?->nota) }}"
                                    class="w-24 rounded-lg border-gray-300 text-sm">
                            </div>

                            <div class="w-20 flex justify-center">
                                <input type="hidden" name="notas[{{ $i }}][falta_na_avaliacao]" value="0">
                                <input type="checkbox" name="notas[{{ $i }}][falta_na_avaliacao]" value="1"
                                    class="h-4 w-4"
                                    @checked(old('notas.'.$i.'.falta_na_avaliacao', $n?->falta_na_avaliacao))>
                            </div>

                            <div class="flex-1">
                                <textarea name="notas[{{ $i }}][observacao]" rows="2"
                                    placeholder="Obs…"
                                    class="w-full rounded-lg border-gray-300 text-sm">{{ old('notas.'.$i.'.observacao', $n?->observacao) }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <x-action-button type="submit">Salvar notas</x-action-button>
        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('buscaAluno');
            const limpar = document.getElementById('limparBusca');
            const tbody = document.getElementById('tabelaNotas');
            if (!input || !tbody) return;

            function apply() {
                const term = (input.value || '').trim().toLowerCase();
                const rows = tbody.querySelectorAll('[data-aluno]');
                rows.forEach(r => {
                    const name = r.getAttribute('data-aluno') || '';
                    r.style.display = !term || name.includes(term) ? '' : 'none';
                });
            }
            input.addEventListener('input', apply);
            if (limpar) limpar.addEventListener('click', () => { input.value = ''; apply(); input.focus(); });
        })();
    </script>
</x-sigem-layout>
