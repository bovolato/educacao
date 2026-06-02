<x-sigem-layout title="Editar notas do bimestre">
    <x-page-header title="Notas do bimestre"
        :subtitle="$notaBimestre->turma?->nome"
        :back-route="route('professor.notas-bimestre.index', ['turma_id' => $notaBimestre->turma_id])"
        back-label="Voltar"/>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="text-sm text-gray-700">
                <span class="font-semibold">Bimestre:</span> {{ $notaBimestre->periodo }}
                <span class="mx-1">·</span>
                <span class="font-semibold">Período:</span>
                {{ $notaBimestre->data_inicio?->format('d/m/Y') }} — {{ $notaBimestre->data_fim?->format('d/m/Y') }}
                <span class="mx-1">·</span>
                <span class="text-gray-600">{{ $disciplinas->count() }} disciplina(s)</span>
            </div>
            <div class="flex items-center gap-2">
                <input id="buscaAlunoNotasBim" type="text" placeholder="Buscar aluno…"
                    class="w-64 max-w-full rounded-xl border-gray-300 text-sm" />
                <button type="button" id="limparBuscaNotasBim" class="text-sm text-gray-600 hover:underline">Limpar</button>
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">Dica: deixe em branco quando não houver nota lançada. A <strong>média final</strong> é manual.</p>
    </div>

    <form method="POST" action="{{ route('professor.notas-bimestre.update', $notaBimestre) }}">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
            <div class="overflow-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3 sticky left-0 bg-gray-50 z-10">Aluno</th>
                            @foreach($disciplinas as $d)
                                <th class="px-5 py-3 whitespace-nowrap">{{ $d->nome }}</th>
                            @endforeach
                            <th class="px-5 py-3 whitespace-nowrap">Média final</th>
                            <th class="px-5 py-3">Observação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tabelaNotasBim">
                        @foreach($itens as $i => $item)
                            @php
                                $nome = $item->matricula?->aluno?->usuario?->nome ?? '—';
                                $map = $item->disciplinas->keyBy('disciplina_id');
                            @endphp
                            <tr data-aluno="{{ strtolower($nome) }}">
                                <td class="px-5 py-3 font-medium text-gray-900 sticky left-0 bg-white z-10">
                                    {{ $nome }}
                                    <input type="hidden" name="itens[{{ $i }}][id]" value="{{ $item->id }}">
                                </td>
                                @foreach($disciplinas as $d)
                                    @php $row = $map->get($d->id); @endphp
                                    <td class="px-5 py-3">
                                        <input type="number" step="0.01" min="0" max="1000"
                                            name="itens[{{ $i }}][notas][{{ $d->id }}]"
                                            value="{{ old("itens.$i.notas.".$d->id, $row?->nota) }}"
                                            class="w-24 rounded-xl border-gray-300 text-sm" />
                                    </td>
                                @endforeach
                                <td class="px-5 py-3">
                                    <input type="number" step="0.01" min="0" max="1000"
                                        name="itens[{{ $i }}][media_final]"
                                        value="{{ old("itens.$i.media_final", $item->media_final) }}"
                                        class="w-28 rounded-xl border-gray-300 text-sm" />
                                </td>
                                <td class="px-5 py-3 min-w-[18rem]">
                                    <input type="text"
                                        name="itens[{{ $i }}][observacao]"
                                        value="{{ old("itens.$i.observacao", $item->observacao) }}"
                                        placeholder="Anotações rápidas…"
                                        class="w-full rounded-xl border-gray-300 text-sm" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('buscaAlunoNotasBim');
            const limpar = document.getElementById('limparBuscaNotasBim');
            const tbody = document.getElementById('tabelaNotasBim');
            if (!tbody) return;

            function apply() {
                const term = (input?.value || '').trim().toLowerCase();
                tbody.querySelectorAll('tr[data-aluno]').forEach(r => {
                    const name = r.getAttribute('data-aluno') || '';
                    r.style.display = !term || name.includes(term) ? '' : 'none';
                });
            }

            if (input) input.addEventListener('input', apply);
            if (limpar) limpar.addEventListener('click', () => { if (input) { input.value = ''; apply(); input.focus(); } });
        })();
    </script>
</x-sigem-layout>

