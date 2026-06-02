<x-sigem-layout title="Criar lista de notas">
    <x-page-header title="Criar lista de notas (bimestre)"
        :back-route="route('professor.notas-bimestre.index', ['turma_id' => $turma->id])"
        back-label="Voltar"/>

    <div class="mb-4 rounded-2xl border border-indigo-100 bg-indigo-50/80 px-4 py-3 text-sm text-indigo-950">
        <span class="font-semibold">Contexto:</span>
        {{ $turma->nome }}
        <span class="mx-1">·</span><span class="font-medium">Bimestre:</span> {{ $periodoAtual }}
        <span class="mx-1">·</span><span class="text-gray-700">{{ $disciplinas->count() }} disciplina(s) vinculada(s)</span>
    </div>

    <form method="POST" action="{{ route('professor.notas-bimestre.store') }}" class="max-w-2xl space-y-5">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma->id }}">

        <x-form-field label="Data início do bimestre" name="data_inicio" required>
            <input type="date" name="data_inicio" value="{{ old('data_inicio') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Data fim do bimestre" name="data_fim" required>
            <input type="date" name="data_fim" value="{{ old('data_fim') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.notas-bimestre.index', ['turma_id' => $turma->id]) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Criar lista</x-action-button>
        </div>
    </form>
</x-sigem-layout>

