<x-sigem-layout title="Nova aula">
    <x-page-header title="Registrar aula"
        :subtitle="!empty($polivalente) ? 'Turma polivalente — uma aula por data para a turma' : null"
        :back-route="!empty($polivalente)
            ? route('professor.aulas.index', ['turma_id' => $turma_id])
            : route('professor.aulas.index', ['turma_id' => $turma_id, 'disciplina_id' => $disciplina_id])"
        back-label="Voltar"/>

    <form method="POST" action="{{ route('professor.aulas.store') }}" class="max-w-2xl space-y-5">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma_id }}">
        <input type="hidden" name="disciplina_id" value="{{ $disciplina_id }}">
        <x-form-field label="Data da aula" name="data_aula" required>
            <input type="date" name="data_aula" value="{{ old('data_aula', now()->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Início" name="hora_inicio">
            <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Fim" name="hora_fim">
            <input type="time" name="hora_fim" value="{{ old('hora_fim') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Status" name="status" required>
            <select name="status" class="w-full rounded-xl border-gray-300 text-sm">
                @foreach(['prevista','realizada','cancelada'] as $st)
                    <option value="{{ $st }}" @selected(old('status', 'realizada') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button
                href="{{ !empty($polivalente)
                    ? route('professor.aulas.index', ['turma_id' => $turma_id])
                    : route('professor.aulas.index', ['turma_id' => $turma_id, 'disciplina_id' => $disciplina_id]) }}"
                variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
