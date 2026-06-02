<x-sigem-layout title="Nova anotação">
    <x-page-header title="Nova anotação" :back-route="route('professor.anotacoes.index')" back-label="Lista"/>

    @php
        $disabled = empty($alunosOptions);
        $turmaHidden = (int) ($turmaId ?? request('turma_id'));
    @endphp

    <div class="max-w-2xl space-y-5">
        <form method="GET" action="{{ route('professor.anotacoes.create') }}" class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:items-end">
                <div class="sm:col-span-2">
                    <x-form-field label="Turma" name="turma_id" required>
                        <select name="turma_id" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">Selecione…</option>
                            @foreach(($turmasOptions ?? []) as $id => $nome)
                                <option value="{{ $id }}" @selected((string) request('turma_id', $turmaId) === (string) $id)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </x-form-field>
                </div>
                <div class="flex justify-end">
                    <x-action-button type="submit">Carregar alunos</x-action-button>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Dica: selecione a turma e clique em <strong>Carregar alunos</strong> para preencher o campo Aluno.</p>
        </form>

        <form method="POST" action="{{ route('professor.anotacoes.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $turmaHidden }}">

            <x-form-field label="Aluno" name="matricula_id" required>
                <select name="matricula_id" class="w-full rounded-xl border-gray-300 text-sm" {{ $disabled ? 'disabled' : '' }}>
                    <option value="">{{ $disabled ? 'Selecione uma turma acima…' : 'Selecione…' }}</option>
                    @foreach(($alunosOptions ?? []) as $id => $nome)
                        <option value="{{ $id }}" @selected((string) old('matricula_id', $matriculaId) === (string) $id)>{{ $nome }}</option>
                    @endforeach
                </select>
            </x-form-field>

            <x-form-field label="Assunto" name="assunto" required>
                <input type="text" name="assunto" value="{{ old('assunto') }}" class="w-full rounded-xl border-gray-300 text-sm" placeholder="Ex.: Comportamento, recado aos responsáveis…"
                    {{ $disabled ? 'disabled' : '' }}>
            </x-form-field>

            <x-form-field label="Texto" name="texto" required>
                <textarea name="texto" rows="6" class="w-full rounded-xl border-gray-300 text-sm" placeholder="Escreva a anotação…"
                    {{ $disabled ? 'disabled' : '' }}>{{ old('texto') }}</textarea>
            </x-form-field>

            <div class="flex justify-end gap-3">
                <x-action-button href="{{ route('professor.anotacoes.index') }}" variant="secondary">Cancelar</x-action-button>
                @if($disabled)
                    <x-action-button type="submit" disabled>Salvar</x-action-button>
                @else
                    <x-action-button type="submit">Salvar</x-action-button>
                @endif
            </div>
        </form>
    </div>
</x-sigem-layout>

