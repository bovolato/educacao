@php
    $user = Auth::user();
@endphp

@php
    $menus = [];

    // === SECRETARIA MUNICIPAL ===
    if ($user->isSecretariaMunicipal()) {
        $menus = [
            ['group' => 'Municipal', 'items' => [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['route' => 'admin.municipios.index', 'label' => 'Municípios', 'icon' => 'map-pin'],
                ['route' => 'admin.escolas.index', 'label' => 'Escolas', 'icon' => 'school'],
            ]],
            ['group' => 'Acadêmico', 'items' => [
                ['route' => 'academico.turmas.index', 'label' => 'Turmas', 'icon' => 'grid'],
                ['route' => 'pessoas.professores.index', 'label' => 'Professores', 'icon' => 'user-tie'],
                ['route' => 'pessoas.alunos.index', 'label' => 'Alunos', 'icon' => 'users'],
                ['route' => 'academico.matriculas.index', 'label' => 'Matrículas', 'icon' => 'clipboard'],
            ]],
            ['group' => 'Configurações', 'items' => [
                ['route' => 'admin.anos-letivos.index', 'label' => 'Anos Letivos', 'icon' => 'calendar'],
                ['route' => 'admin.series.index', 'label' => 'Séries', 'icon' => 'list'],
                ['route' => 'admin.disciplinas.index', 'label' => 'Disciplinas', 'icon' => 'book'],
                ['route' => 'admin.turnos.index', 'label' => 'Turnos', 'icon' => 'clock'],
            ]],
            ['group' => 'Gestão', 'items' => [
                ['route' => 'admin.usuarios.index', 'label' => 'Usuários', 'icon' => 'user-cog'],
                ['route' => 'relatorios.index', 'label' => 'Relatórios', 'icon' => 'chart'],
                ['route' => 'avisos.index', 'label' => 'Avisos', 'icon' => 'bell'],
            ]],
        ];
    }

    // === GESTOR / SECRETÁRIO ESCOLAR ===
    elseif ($user->isGestorEscolar() || $user->hasRole('secretario_escolar')) {
        $menus = [
            ['group' => 'Escola', 'items' => [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['route' => 'academico.turmas.index', 'label' => 'Turmas', 'icon' => 'grid'],
                ['route' => 'pessoas.professores.index', 'label' => 'Professores', 'icon' => 'user-tie'],
                ['route' => 'pessoas.alunos.index', 'label' => 'Alunos', 'icon' => 'users'],
                ['route' => 'academico.matriculas.index', 'label' => 'Matrículas', 'icon' => 'clipboard'],
            ]],
            ['group' => 'Acadêmico', 'items' => [
                ['route' => 'escola.frequencias.index', 'label' => 'Frequência', 'icon' => 'check-square'],
                ['route' => 'escola.notas.index', 'label' => 'Notas', 'icon' => 'star'],
                ['route' => 'escola.documentos.index', 'label' => 'Documentos', 'icon' => 'file'],
            ]],
            ['group' => 'Outros', 'items' => [
                ['route' => 'avisos.index', 'label' => 'Avisos', 'icon' => 'bell'],
            ]],
        ];
    }

    // === PROFESSOR ===
    elseif ($user->isProfessor()) {
        $menus = [
            ['group' => 'Meu Portal', 'items' => [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['route' => 'professor.turmas.index', 'label' => 'Minhas Turmas', 'icon' => 'grid'],
            ]],
            ['group' => 'Diário', 'items' => [
                ['route' => 'professor.alunos.index', 'label' => 'Alunos', 'icon' => 'users'],
                ['route' => 'professor.frequencias.index', 'label' => 'Frequência', 'icon' => 'check-square'],
                ['route' => 'professor.notas-bimestre.index', 'label' => 'Notas (bimestre)', 'icon' => 'star'],
                ['route' => 'professor.avaliacoes.index', 'label' => 'Avaliações', 'icon' => 'clipboard-check'],
                ['route' => 'professor.aulas.index', 'label' => 'Conteúdo Ministrado', 'icon' => 'book-open'],
                ['route' => 'professor.anotacoes.index', 'label' => 'Anotações', 'icon' => 'file-text'],
            ]],
            ['group' => 'Pedagógico', 'items' => [
                ['route' => 'professor.planos-ensino.index', 'label' => 'Planos de ensino', 'icon' => 'list'],
                ['route' => 'professor.planos.index', 'label' => 'Planos de aula', 'icon' => 'edit'],
                ['route' => 'professor.materiais.index', 'label' => 'Materiais', 'icon' => 'folder'],
                ['route' => 'professor.tarefas.index', 'label' => 'Tarefas', 'icon' => 'tasks'],
            ]],
            ['group' => 'Outros', 'items' => [
                ['route' => 'avisos.index', 'label' => 'Avisos', 'icon' => 'bell'],
            ]],
        ];
    }

    // === ALUNO / RESPONSÁVEL ===
    elseif ($user->isAluno() || $user->isResponsavel()) {
        $menus = [
            ['group' => 'Meu Painel', 'items' => [
                ['route' => 'dashboard', 'label' => 'Painel', 'icon' => 'home'],
                ['route' => 'portal.notas', 'label' => 'Notas', 'icon' => 'star'],
                ['route' => 'portal.frequencia', 'label' => 'Frequência', 'icon' => 'check-square'],
                ['route' => 'portal.boletim', 'label' => 'Boletim', 'icon' => 'file-text'],
            ]],
            ['group' => 'Escola', 'items' => [
                ['route' => 'portal.aulas', 'label' => 'Aulas e Conteúdos', 'icon' => 'book-open'],
                ['route' => 'portal.tarefas', 'label' => 'Tarefas', 'icon' => 'tasks'],
                ['route' => 'portal.materiais', 'label' => 'Materiais', 'icon' => 'folder'],
                ['route' => 'portal.avisos', 'label' => 'Avisos', 'icon' => 'bell'],
                ['route' => 'portal.documentos', 'label' => 'Documentos', 'icon' => 'file'],
            ]],
        ];
    }

    // === DEFAULT ===
    else {
        $menus = [
            ['group' => 'Geral', 'items' => [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ]],
        ];
    }
@endphp

@foreach($menus as $group)
    <div class="mb-4">
        <p x-show="!sidebarCollapsed" class="px-3 mb-1 text-xs font-semibold text-indigo-400 uppercase tracking-widest">
            {{ $group['group'] }}
        </p>
        @foreach($group['items'] as $item)
            @php
                // Uma única resolução de URL e um routeIs (evita Route::has + route() duplicados por item)
                $url = route($item['route']);
                $isActive = request()->routeIs($item['route'], $item['route'] . '.*');
            @endphp
            <a href="{{ $url }}"
               class="{{ $isActive
                    ? 'bg-indigo-700 text-white'
                    : 'text-indigo-200 hover:bg-indigo-800 hover:text-white'
               }} flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group text-sm">
                @include('layouts.partials.sidebar-icon', ['icon' => $item['icon']])
                <span x-show="!sidebarCollapsed" class="truncate">{{ $item['label'] }}</span>
                @if($isActive)
                    <span x-show="!sidebarCollapsed" class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-300"></span>
                @endif
            </a>
        @endforeach
    </div>
@endforeach
