<x-sigem-layout :title="'Usuário do Professor'">
    <x-page-header :title="'Usuário do Professor'" :subtitle="$professor->nome" :back-route="route('pessoas.professores.show', $professor)" back-label="Voltar">
    </x-page-header>

    @if(! $professor->pessoa)
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <strong>Cadastro incompleto:</strong> não há registro de pessoa vinculado a este professor. Não é possível vincular login.
        </div>
    @endif

    @if($usuarioVinculado)
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Usuário já vinculado</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-0.5">Nome</p>
                    <p class="font-medium text-gray-900">{{ $usuarioVinculado->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-0.5">Username</p>
                    <p class="font-mono font-medium text-gray-900">{{ $usuarioVinculado->username ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-0.5">Email</p>
                    <p class="font-medium text-gray-900">{{ $usuarioVinculado->email ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-0.5">Perfis</p>
                    <p class="font-medium text-gray-700">
                        {{ $usuarioVinculado->roles?->pluck('name')->implode(', ') ?: '—' }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 max-w-2xl">
            <h3 class="font-semibold text-gray-800 mb-2">Criar login para o professor</h3>
            <p class="text-sm text-gray-500 mb-5">
                O vínculo é feito pela <code class="text-xs">pessoa_id</code>. Este formulário criará um usuário com perfil <strong>professor</strong> e associará à pessoa deste professor.
            </p>

            <form method="POST" action="{{ route('pessoas.professores.usuario.store', $professor) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email', $emailPrefill) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input name="username" type="text" value="{{ old('username', $usernamePrefill) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('username')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input name="password" type="password"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="ativo" value="1" {{ old('ativo', '1') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Usuário ativo
                </label>

                <div class="pt-2 flex items-center gap-3">
                    <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>'>
                        Criar login
                    </x-action-button>
                    <a href="{{ route('pessoas.professores.show', $professor) }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                </div>
            </form>
        </div>
    @endif
</x-sigem-layout>

