<x-sigem-layout title="Editar Matrícula">
    <x-page-header :title="'Matrícula: ' . $matricula->aluno->pessoa->nome" :back-route="route('academico.matriculas.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('academico.matriculas.update', $matricula) }}">
        @csrf @method('PATCH')
        <x-form-card title="Atualizar Matrícula">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase mb-1">Aluno</p>
                        <p class="font-semibold text-gray-800">{{ $matricula->aluno->pessoa->nome }}</p>
                        <p class="text-sm text-gray-500">RA: {{ $matricula->aluno->ra ?? '—' }} · Matrícula Nº {{ $matricula->numero_matricula }}</p>
                    </div>
                </div>
                <x-form-field label="Turma" name="turma_id" required>
                    <select name="turma_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        @foreach($turmas as $t)
                            <option value="{{ $t->id }}" @selected(old('turma_id', $matricula->turma_id) == $t->id)>{{ $t->nome }} — {{ $t->serie->nome ?? '' }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Situação" name="situacao" required>
                    <select name="situacao" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        @foreach(['ativa' => 'Ativa', 'transferida' => 'Transferida', 'evadida' => 'Evadida', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('situacao', $matricula->situacao) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <input type="hidden" name="aluno_id" value="{{ $matricula->aluno_id }}">
                <input type="hidden" name="escola_id" value="{{ $matricula->escola_id }}">
                <input type="hidden" name="ano_letivo_id" value="{{ $matricula->ano_letivo_id }}">
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('academico.matriculas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Alterações</x-action-button>
        </div>
    </form>
</x-sigem-layout>
