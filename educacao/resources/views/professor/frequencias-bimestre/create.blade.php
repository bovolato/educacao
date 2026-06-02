<x-sigem-layout title="Criar lista de presença">
    <x-page-header title="Criar lista de presença (bimestre)"
        :back-route="route('professor.frequencias.index', array_filter(['turma_id' => $turma->id, 'disciplina_id' => request('disciplina_id')]))"
        back-label="Voltar"/>

    <div class="mb-4 rounded-2xl border border-indigo-100 bg-indigo-50/80 px-4 py-3 text-sm text-indigo-950">
        <span class="font-semibold">Contexto:</span>
        {{ $turma->nome }}
        @if($turma->polivalente)
            <span class="mx-1">·</span><span class="font-medium">Polivalente</span>
        @elseif($disciplina)
            <span class="mx-1">·</span>{{ $disciplina->nome }}
        @endif
        <span class="mx-1">·</span><span class="font-medium">Bimestre:</span> {{ $periodoAtual }}
    </div>

    <form method="POST" action="{{ route('professor.frequencias.store') }}" class="max-w-2xl space-y-5">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma->id }}">
        <input type="hidden" name="disciplina_id" value="{{ $disciplina_id }}">

        <x-form-field label="Data início do bimestre" name="data_inicio" required>
            <input type="date" name="data_inicio" value="{{ old('data_inicio') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Data fim do bimestre" name="data_fim" required>
            <input type="date" name="data_fim" value="{{ old('data_fim') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.frequencias.index', array_filter(['turma_id' => $turma->id, 'disciplina_id' => request('disciplina_id')])) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Criar lista</x-action-button>
        </div>
    </form>
</x-sigem-layout>

