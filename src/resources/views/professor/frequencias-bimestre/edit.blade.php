<x-sigem-layout title="Editar lista de presença">
    <x-page-header title="Frequência do bimestre"
        :subtitle="$frequenciaBimestre->turma?->nome"
        :back-route="route('professor.frequencias.index', array_filter(['turma_id' => $frequenciaBimestre->turma_id, 'disciplina_id' => request('disciplina_id')]))"
        back-label="Voltar"/>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="text-sm text-gray-700">
                <span class="font-semibold">Bimestre:</span> {{ $frequenciaBimestre->periodo }}
                <span class="mx-1">·</span>
                <span class="font-semibold">Período:</span>
                {{ $frequenciaBimestre->data_inicio?->format('d/m/Y') }} — {{ $frequenciaBimestre->data_fim?->format('d/m/Y') }}
                @if($frequenciaBimestre->disciplina)
                    <span class="mx-1">·</span><span class="text-gray-600">{{ $frequenciaBimestre->disciplina->nome }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <input id="buscaAlunoBim" type="text" placeholder="Buscar aluno…"
                    class="w-64 max-w-full rounded-xl border-gray-300 text-sm" />
                <button type="button" id="limparBuscaBim" class="text-sm text-gray-600 hover:underline">Limpar</button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('professor.frequencias.update', $frequenciaBimestre) }}">
        @csrf
        @method('PUT')
        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-5 py-3">Aluno</th>
                        <th class="px-5 py-3">Presenças</th>
                        <th class="px-5 py-3">Faltas</th>
                        <th class="px-5 py-3">Justificadas</th>
                        <th class="px-5 py-3">Observação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tabelaBim">
                    @foreach($itens as $i => $item)
                        @php $nome = $item->matricula?->aluno?->pessoa?->nome ?? '—'; @endphp
                        <tr data-aluno="{{ strtolower($nome) }}">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $nome }}</td>
                            <td class="px-5 py-3">
                                <input type="hidden" name="itens[{{ $i }}][id]" value="{{ $item->id }}">
                                <input type="number" min="0" max="999" name="itens[{{ $i }}][presencas]" value="{{ old("itens.$i.presencas", $item->presencas) }}"
                                    class="w-24 rounded-xl border-gray-300 text-sm" />
                            </td>
                            <td class="px-5 py-3">
                                <input type="number" min="0" max="999" name="itens[{{ $i }}][faltas]" value="{{ old("itens.$i.faltas", $item->faltas) }}"
                                    class="w-24 rounded-xl border-gray-300 text-sm" />
                            </td>
                            <td class="px-5 py-3">
                                <input type="number" min="0" max="999" name="itens[{{ $i }}][faltas_justificadas]" value="{{ old("itens.$i.faltas_justificadas", $item->faltas_justificadas) }}"
                                    class="w-28 rounded-xl border-gray-300 text-sm" />
                            </td>
                            <td class="px-5 py-3">
                                <input type="text" name="itens[{{ $i }}][observacao]" value="{{ old("itens.$i.observacao", $item->observacao) }}"
                                    placeholder="Anotações rápidas…"
                                    class="w-full rounded-xl border-gray-300 text-sm" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-6">
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('buscaAlunoBim');
            const limpar = document.getElementById('limparBuscaBim');
            const tbody = document.getElementById('tabelaBim');
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

