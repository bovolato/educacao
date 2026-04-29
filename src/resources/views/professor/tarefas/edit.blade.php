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
                        <div class="text-sm font-semibold text-gray-900">Status por aluno</div>
                        <div class="text-xs text-gray-500">Defina a entrega por aluno (pendente / entregue / não entregou).</div>
                    </div>
                    <div class="text-xs text-gray-500">
                        <span id="contadorMarcados">0</span>/{{ $matriculas->count() }} com status definido
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
                                $st = $reg?->status ?? 'pendente';
                                if ($st === 'fez') $st = 'entregue';
                                if ($st === 'nao_fez') $st = 'nao_entregue';
                                $nome = $m->aluno?->pessoa?->nome ?? '—';
                            @endphp
                            <div class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50" data-aluno="{{ strtolower($nome) }}">
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $nome }}</div>
                                    <div class="text-xs text-gray-500">Matrícula: {{ $m->numero_matricula ?? $m->id }}</div>
                                </div>
                                <div class="shrink-0">
                                    <select name="status[{{ $m->id }}]" class="rounded-xl border-gray-300 text-sm status-select">
                                        <option value="pendente" @selected(old('status.'.$m->id, $st) === 'pendente')>Pendente</option>
                                        <option value="entregue" @selected(old('status.'.$m->id, $st) === 'entregue')>Entregue</option>
                                        <option value="nao_entregue" @selected(old('status.'.$m->id, $st) === 'nao_entregue')>Não entregou</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" id="marcarTodosEntregue" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm">Marcar todos como entregue</button>
                    <button type="button" id="marcarTodosPendente" class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 text-sm">Voltar todos para pendente</button>
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
            const btnAll = document.getElementById('marcarTodosEntregue');
            const btnNone = document.getElementById('marcarTodosPendente');
            const contador = document.getElementById('contadorMarcados');
            if (!lista) return;

            function updateCount() {
                let n = 0;
                lista.querySelectorAll('select.status-select').forEach(s => {
                    if (s.value && s.value !== 'pendente') n++;
                });
                if (contador) contador.textContent = String(n);
            }

            function applyFilter() {
                const term = (input?.value || '').trim().toLowerCase();
                lista.querySelectorAll('[data-aluno]').forEach(el => {
                    const name = el.getAttribute('data-aluno') || '';
                    el.style.display = !term || name.includes(term) ? '' : 'none';
                });
            }

            lista.querySelectorAll('select.status-select').forEach(s => s.addEventListener('change', updateCount));
            if (input) input.addEventListener('input', applyFilter);
            if (limpar) limpar.addEventListener('click', () => { if (input) { input.value = ''; applyFilter(); input.focus(); } });
            if (btnAll) btnAll.addEventListener('click', () => { lista.querySelectorAll('select.status-select').forEach(s => s.value = 'entregue'); updateCount(); });
            if (btnNone) btnNone.addEventListener('click', () => { lista.querySelectorAll('select.status-select').forEach(s => s.value = 'pendente'); updateCount(); });

            updateCount();
        })();
    </script>
</x-sigem-layout>
