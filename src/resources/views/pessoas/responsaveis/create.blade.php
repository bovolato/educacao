<x-sigem-layout title="Novo Responsável">
    <x-page-header title="Novo Responsável" :back-route="route('pessoas.responsaveis.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('pessoas.responsaveis.store') }}">
        @csrf
        <x-form-card title="Dados Pessoais e de Contato">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome Completo" name="nome" required>
                        <input type="text" name="nome" value="{{ old('nome') }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="CPF" name="cpf">
                    <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Tipo de Responsável" name="tipo_responsavel" required>
                    <select name="tipo_responsavel" class="w-full px-4 py-2.5 rounded-xl border @error('tipo_responsavel') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach(['Mãe', 'Pai', 'Avó', 'Avô', 'Tio(a)', 'Tutor(a)', 'Cônjuge', 'Outro'] as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_responsavel') === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Celular" name="telefone">
                    <input type="text" name="telefone" value="{{ old('telefone') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="E-mail" name="email_contato">
                    <input type="email" name="email_contato" value="{{ old('email_contato') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="responsavel_legal" id="responsavel_legal" value="1" @checked(old('responsavel_legal', true))
                            class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <label for="responsavel_legal" class="text-sm text-gray-700">Responsável Legal</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="recebe_notificacao" id="recebe_notificacao" value="1" @checked(old('recebe_notificacao', true))
                            class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <label for="recebe_notificacao" class="text-sm text-gray-700">Recebe Notificações</label>
                    </div>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('pessoas.responsaveis.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Responsável</x-action-button>
        </div>
    </form>
</x-sigem-layout>
