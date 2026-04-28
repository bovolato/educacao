<x-sigem-layout title="Lançar frequência">
    <x-page-header title="Frequência da aula" :subtitle="$aula->turma->nome"
        :back-route="$aula->turma->polivalente
            ? route('professor.frequencias.index', ['turma_id' => $aula->turma_id])
            : route('professor.frequencias.index', ['turma_id' => $aula->turma_id, 'disciplina_id' => $aula->disciplina_id])"
        back-label="Voltar"/>

    <x-professor-context-bar
        :turma-nome="$aula->turma?->nome"
        :disciplina-nome="$aula->turma?->polivalente ? null : ($aula->disciplina?->nome ?? null)"
        :polivalente="(bool) ($aula->turma?->polivalente ?? false)"
        :data-label="'Aula: ' . ($aula->data_aula?->format('d/m/Y') ?? '—')"
    />

    @php
        $total = $matriculas->count();
        $cont = ['presente' => 0, 'falta' => 0, 'justificada' => 0, 'atraso' => 0, 'pendente' => 0];
        foreach ($matriculas as $m) {
            $f = $freqPorMatricula->get($m->id);
            $sit = $f?->situacao ?? 'pendente';
            $cont[$sit] = ($cont[$sit] ?? 0) + 1;
        }
    @endphp

    <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-700">
                <x-badge color="gray">{{ $cont['pendente'] }} pendente(s)</x-badge>
                <x-badge color="green">{{ $cont['presente'] }} presente(s)</x-badge>
                <x-badge color="red">{{ $cont['falta'] }} falta(s)</x-badge>
                <x-badge color="yellow">{{ $cont['justificada'] }} justificada(s)</x-badge>
                <x-badge color="indigo">{{ $cont['atraso'] }} atraso(s)</x-badge>
                <span class="text-xs text-gray-500 ml-1">({{ $total }} alunos)</span>
            </div>
            <div class="flex items-center gap-2">
                <input id="buscaAluno" type="text" placeholder="Buscar aluno…"
                    class="w-64 max-w-full rounded-xl border-gray-300 text-sm" />
                <button type="button" id="limparBusca" class="text-sm text-gray-600 hover:underline">Limpar</button>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
            <button type="button" data-set-all="presente" class="px-3 py-1.5 rounded-lg bg-green-50 text-green-800 text-sm hover:bg-green-100">Todos presentes</button>
            <button type="button" data-set-all="falta" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-800 text-sm hover:bg-red-100">Todos faltas</button>
            <button type="button" data-set-all="justificada" class="px-3 py-1.5 rounded-lg bg-yellow-50 text-yellow-800 text-sm hover:bg-yellow-100">Todos justificados</button>
        </div>
    </div>

    <form method="POST" action="{{ route('professor.frequencias.aula.update', $aula) }}">
        @csrf
        @method('PUT')
        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-5 py-3">Aluno</th>
                        <th class="px-5 py-3">Situação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tabelaFreq">
                    @foreach($matriculas as $i => $mat)
                        @php $f = $freqPorMatricula->get($mat->id); @endphp
                        <tr data-aluno="{{ strtolower($mat->aluno?->pessoa?->nome ?? '') }}">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $mat->aluno?->pessoa?->nome ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <input type="hidden" name="frequencias[{{ $i }}][matricula_id]" value="{{ $mat->id }}">
                                <select name="frequencias[{{ $i }}][situacao]" class="rounded-lg border-gray-300 text-sm freq-select">
                                    <option value="" @selected(($f?->situacao ?? '') === '')>Selecione…</option>
                                    @foreach(['presente','falta','justificada','atraso'] as $sit)
                                        <option value="{{ $sit }}" @selected(($f?->situacao ?? '') === $sit)>{{ ucfirst($sit) }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-6">
            <x-action-button type="submit">Salvar frequência</x-action-button>
        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('buscaAluno');
            const limpar = document.getElementById('limparBusca');
            const tbody = document.getElementById('tabelaFreq');
            if (!tbody) return;

            function apply() {
                const term = (input?.value || '').trim().toLowerCase();
                const rows = tbody.querySelectorAll('tr[data-aluno]');
                rows.forEach(r => {
                    const name = r.getAttribute('data-aluno') || '';
                    r.style.display = !term || name.includes(term) ? '' : 'none';
                });
            }
            if (input) input.addEventListener('input', apply);
            if (limpar) limpar.addEventListener('click', () => { if (input) { input.value = ''; apply(); input.focus(); } });

            document.querySelectorAll('button[data-set-all]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const val = btn.getAttribute('data-set-all');
                    tbody.querySelectorAll('select.freq-select').forEach(sel => { sel.value = val; });
                });
            });
        })();
    </script>
</x-sigem-layout>
