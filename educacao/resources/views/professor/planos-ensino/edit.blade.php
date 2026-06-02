<x-sigem-layout title="Editar plano de ensino">
    <x-page-header title="Editar plano de ensino" :back-route="route('professor.planos-ensino.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.planos-ensino.update', $planoEnsino) }}" class="max-w-2xl space-y-5">
        @csrf @method('PUT')
        <x-form-field label="Ano letivo" name="ano_letivo_id" required>
            <select name="ano_letivo_id" class="w-full rounded-xl border-gray-300 text-sm">
                @foreach($anos as $a)
                    <option value="{{ $a->id }}" @selected(old('ano_letivo_id', $planoEnsino->ano_letivo_id) == $a->id)>{{ $a->descricao }}</option>
                @endforeach
            </select>
        </x-form-field>
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo', $planoEnsino->titulo) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Objetivos" name="objetivos">
            <textarea name="objetivos" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('objetivos', $planoEnsino->objetivos) }}</textarea>
        </x-form-field>
        <x-form-field label="Metodologia" name="metodologia">
            <textarea name="metodologia" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('metodologia', $planoEnsino->metodologia) }}</textarea>
        </x-form-field>
        <x-form-field label="Critérios de avaliação" name="criterios_avaliacao">
            <textarea name="criterios_avaliacao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('criterios_avaliacao', $planoEnsino->criterios_avaliacao) }}</textarea>
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.planos-ensino.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
