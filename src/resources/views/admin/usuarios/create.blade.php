<x-sigem-layout title="Novo Usuário">
    <x-page-header title="Novo Usuário" :back-route="route('admin.usuarios.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf
        <x-form-card title="Dados do Usuário">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome Completo" name="name" required>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('name') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="E-mail" name="email" required>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Username / Login" name="username">
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('username') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Senha" name="password" required>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-xl border @error('password') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Perfil de Acesso" name="perfil">
                    <select name="perfil" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Sem perfil</option>
                        @foreach($perfis as $p)
                            <option value="{{ $p->name }}" @selected(old('perfil') === $p->name)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Município" name="municipio_id">
                    <select name="municipio_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($municipios as $m)
                            <option value="{{ $m->id }}" @selected(old('municipio_id') == $m->id)>{{ $m->nome }}/{{ $m->uf }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Escola" name="escola_id">
                    <select name="escola_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Nenhuma</option>
                        @foreach($escolas as $e)
                            <option value="{{ $e->id }}" @selected(old('escola_id') == $e->id)>{{ $e->nome }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <div class="flex items-center gap-3 mt-2">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', true))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Usuário Ativo</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.usuarios.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
