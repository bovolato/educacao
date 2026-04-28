<x-sigem-layout title="Nova tarefa">
    <x-page-header title="Nova tarefa" :back-route="route('professor.tarefas.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.tarefas.store') }}" class="max-w-xl space-y-5">
        @csrf
        @if(!empty($turma_id))
            <input type="hidden" name="turma_id" value="{{ $turma_id }}">
        @else
            <x-form-field label="Turma" name="turma_id" required>
                <select name="turma_id" id="turma_id" class="w-full rounded-xl border-gray-300 text-sm">
                    <option value="" disabled @selected(old('turma_id') === null)>Selecione…</option>
                    @foreach(($turmasOptions ?? []) as $id => $nome)
                        <option value="{{ $id }}" @selected(old('turma_id') == $id)>{{ $nome }}</option>
                    @endforeach
                </select>
            </x-form-field>
        @endif
        @if(!empty($disciplina_id))
            <input type="hidden" name="disciplina_id" value="{{ $disciplina_id }}">
        @else
            <x-form-field label="Disciplina" name="disciplina_id" required>
                <select name="disciplina_id" id="disciplina_id" class="w-full rounded-xl border-gray-300 text-sm">
                    <option value="" disabled @selected(old('disciplina_id') === null)>Selecione…</option>
                    @foreach(($disciplinasOptions ?? []) as $id => $nome)
                        <option value="{{ $id }}" @selected(old('disciplina_id') == $id)>{{ $nome }}</option>
                    @endforeach
                </select>
            </x-form-field>
        @endif
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Descrição" name="descricao">
            <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao') }}</textarea>
        </x-form-field>
        <x-form-field label="Data postagem" name="data_postagem">
            <input type="date" name="data_postagem" value="{{ old('data_postagem', now()->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Data entrega" name="data_entrega">
            <input type="date" name="data_entrega" value="{{ old('data_entrega') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Valor (pontos)" name="valor">
            <input type="number" step="0.01" name="valor" value="{{ old('valor') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.tarefas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>

    @if(empty($turma_id) && empty($disciplina_id))
        <script>
            (function () {
                const turmaEl = document.getElementById('turma_id');
                const discEl = document.getElementById('disciplina_id');
                if (!turmaEl || !discEl) return;

                const disciplinasByTurma = @json(($disciplinasByTurma ?? collect())->map(fn ($c) => $c->toArray())->toArray());
                const oldDisc = @json(old('disciplina_id'));

                function renderDisciplinas() {
                    const turmaId = turmaEl.value;
                    const items = disciplinasByTurma?.[turmaId] || {};
                    const current = discEl.value;
                    discEl.innerHTML = '';

                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.disabled = true;
                    placeholder.selected = true;
                    placeholder.textContent = 'Selecione…';
                    discEl.appendChild(placeholder);

                    Object.entries(items).forEach(([id, nome]) => {
                        const opt = document.createElement('option');
                        opt.value = id;
                        opt.textContent = nome;
                        discEl.appendChild(opt);
                    });

                    const prefer = oldDisc || current;
                    if (prefer && items[String(prefer)]) {
                        discEl.value = String(prefer);
                        placeholder.selected = false;
                    }
                }

                turmaEl.addEventListener('change', renderDisciplinas);
                if (turmaEl.value) renderDisciplinas();
            })();
        </script>
    @endif
</x-sigem-layout>
