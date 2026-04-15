@php
use App\Models\Pessoas\Professor;
use App\Models\Academico\{Turma, Aula};

$professor = Auth::user()->pessoa?->professor;
$totalTurmas = $professor ? $professor->turmas()->where('status', 'ativa')->count() : 0;
$aulas_hoje  = $professor ? Aula::where('professor_id', $professor->id)->whereDate('data_aula', today())->count() : 0;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalTurmas }}</p>
        <p class="text-sm text-gray-500 mt-1">Turmas vinculadas</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $aulas_hoje }}</p>
        <p class="text-sm text-gray-500 mt-1">Aulas hoje ({{ today()->format('d/m') }})</p>
    </div>
</div>

<div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white">
    <h3 class="font-semibold text-lg mb-2">Ações do dia</h3>
    <p class="text-indigo-200 text-sm mb-4">Gerencie suas atividades pedagógicas</p>
    <div class="flex flex-wrap gap-3">
        <a href="#" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Lançar Frequência</a>
        <a href="#" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Lançar Notas</a>
        <a href="#" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Registrar Conteúdo</a>
    </div>
</div>
