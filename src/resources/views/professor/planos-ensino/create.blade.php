<x-sigem-layout title="Novo plano de ensino">
    <x-page-header title="Novo plano de ensino" :back-route="route('professor.planos-ensino.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.planos-ensino.store') }}" class="max-w-2xl space-y-5">
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
        <x-form-field label="Ano letivo" name="ano_letivo_id" required>
            <select name="ano_letivo_id" class="w-full rounded-xl border-gray-300 text-sm">
                @foreach($anos as $a)
                    <option value="{{ $a->id }}" @selected(old('ano_letivo_id') == $a->id)>{{ $a->descricao }}</option>
                @endforeach
            </select>
        </x-form-field>
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Objetivos" name="objetivos">
            <textarea name="objetivos" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('objetivos') }}</textarea>
        </x-form-field>
        <x-form-field label="Metodologia" name="metodologia">
            <textarea name="metodologia" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('metodologia') }}</textarea>
        </x-form-field>
        <x-form-field label="Critérios de avaliação" name="criterios_avaliacao">
            <textarea name="criterios_avaliacao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('criterios_avaliacao') }}</textarea>
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.turmas.index') }}" variant="secondary">Cancelar</x-action-button>
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
