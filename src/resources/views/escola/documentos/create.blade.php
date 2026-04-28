<x-sigem-layout title="Emitir documento">
    <x-page-header title="Novo documento" :back-route="route('escola.documentos.index')" back-label="Voltar"/>

    <form method="POST" action="{{ route('escola.documentos.store') }}" class="max-w-xl">
        @csrf
        <x-form-card title="Dados">
            <div class="space-y-5">
                <x-form-field label="Matrícula ativa" name="matricula_id" required>
                    <select name="matricula_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm">
                        <option value="">Selecione...</option>
                        @foreach($matriculas as $m)
                            <option value="{{ $m->id }}" @selected(old('matricula_id') == $m->id)>
                                {{ $m->aluno?->pessoa?->nome ?? 'Aluno' }} — {{ $m->turma?->nome ?? 'Turma' }}
                            </option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Tipo" name="tipo_documento" required>
                    <select name="tipo_documento" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm">
                        @foreach($tipos as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('tipo_documento') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3 mt-6">
            <x-action-button href="{{ route('escola.documentos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Gerar e registrar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
