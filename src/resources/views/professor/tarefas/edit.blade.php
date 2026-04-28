<x-sigem-layout title="Editar tarefa">
    <x-page-header title="Editar tarefa" :back-route="route('professor.tarefas.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.tarefas.update', $tarefa) }}" class="max-w-4xl space-y-6">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="space-y-5">
                <x-form-field label="Título" name="titulo" required>
                    <input type="text" name="titulo" value="{{ old('titulo', $tarefa->titulo) }}" class="w-full rounded-xl border-gray-300 text-sm">
                </x-form-field>
                <x-form-field label="Descrição" name="descricao">
                    <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao', $tarefa->descricao) }}</textarea>
                </x-form-field>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field label="Data postagem" name="data_postagem">
                        <input type="date" name="data_postagem" value="{{ old('data_postagem', $tarefa->data_postagem?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
                    </x-form-field>
                    <x-form-field label="Data entrega" name="data_entrega">
                        <input type="date" name="data_entrega" value="{{ old('data_entrega', $tarefa->data_entrega?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="Valor (pontos)" name="valor">
                    <input type="number" step="0.01" name="valor" value="{{ old('valor', $tarefa->valor) }}" class="w-full rounded-xl border-gray-300 text-sm">
                </x-form-field>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Alunos que fizeram</div>
                        <div class="text-xs text-gray-500">Marque quem realizou a tarefa (checklist rápido).</div>
                    </div>
                    <div class="text-xs text-gray-500">
                        <span id="contadorFez">0</span>/{{ $matriculas->count() }} marcados
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-3">
                    <input id="buscaAlunoTarefa" type="text" placeholder="Buscar aluno…"
                        class="w-full rounded-xl border-gray-300 text-sm" />
                    <button type="button" id="limparBuscaTarefa" class="text-sm text-gray-600 hover:underline">Limpar</button>
                </div>

                <div class="max-h-80 overflow-auto rounded-xl border border-gray-100">
                    <div class="divide-y divide-gray-100" id="listaAlunosTarefa">
                        @foreach($matriculas as $m)
                            @php
                                $reg = $registros->get($m->id);
                                $checked = in_array(($reg?->status ?? ''), ['fez', 'entregue'], true);
                                $nome = $m->aluno?->pessoa?->nome ?? '—';
                            @endphp
                            <label class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50" data-aluno="{{ strtolower($nome) }}">
                                <input type="checkbox" name="fez[]" value="{{ $m->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 fez-checkbox"
                                    @checked($checked) />
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $nome }}</div>
                                    <div class="text-xs text-gray-500">Matrícula: {{ $m->numero_matricula ?? $m->id }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" id="marcarTodosFez" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm">Marcar todos</button>
                    <button type="button" id="desmarcarTodosFez" class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 text-sm">Desmarcar todos</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.tarefas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>

    <script>
        (function () {
            const lista = document.getElementById('listaAlunosTarefa');
            const input = document.getElementById('buscaAlunoTarefa');
            const limpar = document.getElementById('limparBuscaTarefa');
            const btnAll = document.getElementById('marcarTodosFez');
            const btnNone = document.getElementById('desmarcarTodosFez');
            const contador = document.getElementById('contadorFez');
            if (!lista) return;

            function updateCount() {
                const checks = lista.querySelectorAll('input.fez-checkbox');
                let n = 0;
                checks.forEach(c => { if (c.checked) n++; });
                if (contador) contador.textContent = String(n);
            }

            function applyFilter() {
                const term = (input?.value || '').trim().toLowerCase();
                lista.querySelectorAll('[data-aluno]').forEach(el => {
                    const name = el.getAttribute('data-aluno') || '';
                    el.style.display = !term || name.includes(term) ? '' : 'none';
                });
            }

            lista.querySelectorAll('input.fez-checkbox').forEach(c => c.addEventListener('change', updateCount));
            if (input) input.addEventListener('input', applyFilter);
            if (limpar) limpar.addEventListener('click', () => { if (input) { input.value = ''; applyFilter(); input.focus(); } });
            if (btnAll) btnAll.addEventListener('click', () => { lista.querySelectorAll('input.fez-checkbox').forEach(c => c.checked = true); updateCount(); });
            if (btnNone) btnNone.addEventListener('click', () => { lista.querySelectorAll('input.fez-checkbox').forEach(c => c.checked = false); updateCount(); });

            updateCount();
        })();
    </script>
</x-sigem-layout>
