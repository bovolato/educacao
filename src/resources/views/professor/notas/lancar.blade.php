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
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-5 py-3">Aluno</th>
                        <th class="px-5 py-3">Nota</th>
                        <th class="px-5 py-3">Falta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tabelaNotas">
                    @foreach($matriculas as $i => $mat)
                        @php $n = $notasPorMatricula->get($mat->id); @endphp
                        <tr data-aluno="{{ strtolower($mat->aluno?->pessoa?->nome ?? '') }}">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $mat->aluno?->pessoa?->nome ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <input type="hidden" name="notas[{{ $i }}][matricula_id]" value="{{ $mat->id }}">
                                <input type="number" step="0.1" name="notas[{{ $i }}][nota]" value="{{ old('notas.'.$i.'.nota', $n?->nota) }}" class="w-24 rounded-lg border-gray-300 text-sm">
                            </td>
                            <td class="px-5 py-3">
                                <input type="hidden" name="notas[{{ $i }}][falta_na_avaliacao]" value="0">
                                <input type="checkbox" name="notas[{{ $i }}][falta_na_avaliacao]" value="1" @checked(old('notas.'.$i.'.falta_na_avaliacao', $n?->falta_na_avaliacao))>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                const rows = tbody.querySelectorAll('tr[data-aluno]');
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
