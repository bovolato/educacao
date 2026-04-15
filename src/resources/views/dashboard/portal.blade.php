<div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white mb-6">
    <h3 class="font-semibold text-lg mb-1">Portal do Aluno</h3>
    <p class="text-indigo-200 text-sm">Acompanhe sua vida escolar</p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    @foreach([
        ['label' => 'Minhas Notas', 'route' => '#', 'icon' => 'star', 'color' => 'amber'],
        ['label' => 'Frequência', 'route' => '#', 'icon' => 'check', 'color' => 'emerald'],
        ['label' => 'Boletim', 'route' => '#', 'icon' => 'file', 'color' => 'blue'],
        ['label' => 'Aulas', 'route' => '#', 'icon' => 'book', 'color' => 'violet'],
        ['label' => 'Tarefas', 'route' => '#', 'icon' => 'tasks', 'color' => 'rose'],
        ['label' => 'Avisos', 'route' => '#', 'icon' => 'bell', 'color' => 'indigo'],
    ] as $item)
    <a href="{{ $item['route'] }}" class="bg-white rounded-2xl p-5 border border-gray-200 hover:shadow-md transition-shadow text-center group">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center mx-auto mb-3 group-hover:bg-indigo-600 transition-colors">
            <svg class="w-5 h-5 text-indigo-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ $item['label'] }}</p>
    </a>
    @endforeach
</div>
