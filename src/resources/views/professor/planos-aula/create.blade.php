<x-sigem-layout title="Novo plano de aula">
    <x-page-header title="Novo plano de aula" :back-route="route('professor.planos.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.planos.store') }}" class="max-w-2xl space-y-5">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma_id }}">
        <input type="hidden" name="disciplina_id" value="{{ $disciplina_id }}">
        <x-form-field label="Data prevista" name="data_prevista">
            <input type="date" name="data_prevista" value="{{ old('data_prevista') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Objetivos" name="objetivos">
            <textarea name="objetivos" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('objetivos') }}</textarea>
        </x-form-field>
        <x-form-field label="Conteúdo previsto" name="conteudo_previsto">
            <textarea name="conteudo_previsto" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('conteudo_previsto') }}</textarea>
        </x-form-field>
        <x-form-field label="Recursos" name="recursos">
            <textarea name="recursos" rows="2" class="w-full rounded-xl border-gray-300 text-sm">{{ old('recursos') }}</textarea>
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.planos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
