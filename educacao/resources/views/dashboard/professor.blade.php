@php
use Illuminate\Support\Facades\DB;
use App\Models\Academico\Aula;

$professor = Auth::user()->professor;
$totalTurmas = $professor
    ? $professor->turmas()->where('status', 'ativa')->distinct('turmas.id')->count('turmas.id')
    : 0;
$aulas_hoje  = $professor ? Aula::where('professor_id', $professor->id)->whereDate('data_aula', today())->count() : 0;

$primeiroVinculo = null;
if ($professor) {
    $primeiroVinculo = DB::table('turma_professores as tp')
        ->where('tp.professor_id', $professor->id)
        ->join('turmas as t', 't.id', '=', 'tp.turma_id')
        ->join('disciplinas as d', 'd.id', '=', 'tp.disciplina_id')
        ->where('t.status', 'ativa')
        ->orderBy('t.nome')
        ->orderBy('d.nome')
        ->select(['t.id as turma_id', 't.polivalente as turma_polivalente', 'd.id as disciplina_id'])
        ->first();
}

$turmaDisciplinaQuery = $primeiroVinculo
    ? ['turma_id' => $primeiroVinculo->turma_id, 'disciplina_id' => $primeiroVinculo->disciplina_id]
    : [];
$turmaQuery = $primeiroVinculo ? ['turma_id' => $primeiroVinculo->turma_id] : [];

$urlFrequencias = $primeiroVinculo
    ? route('professor.frequencias.index', ($primeiroVinculo->turma_polivalente ? $turmaQuery : $turmaDisciplinaQuery))
    : route('professor.turmas.index');
$urlNotas = $primeiroVinculo
    ? route('professor.notas.index', $turmaDisciplinaQuery)
    : route('professor.turmas.index');
$urlAulasConteudo = $primeiroVinculo
    ? route('professor.aulas.index', ($primeiroVinculo->turma_polivalente ? $turmaQuery : $turmaDisciplinaQuery))
    : route('professor.turmas.index');
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    <a href="{{ route('professor.turmas.index') }}" class="block bg-white rounded-2xl p-5 border border-gray-200 hover:border-indigo-200 hover:shadow-sm transition-shadow">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalTurmas }}</p>
        <p class="text-sm text-gray-500 mt-1">Turmas vinculadas — clique para gerenciar</p>
    </a>

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
    <p class="text-indigo-200 text-sm mb-4">
        @if($primeiroVinculo)
            Atalhos para a primeira turma/disciplina da sua lista (você pode trocar o contexto em cada tela).
        @else
            Você ainda não tem turmas vinculadas. Peça ao gestor da escola para vincular suas disciplinas — depois volte aqui ou acesse <strong>Minhas turmas</strong>.
        @endif
    </p>
    <div class="flex flex-wrap gap-3">
        <a href="{{ $urlFrequencias }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Lançar frequência</a>
        <a href="{{ $urlNotas }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Lançar notas</a>
        <a href="{{ $urlAulasConteudo }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Registrar conteúdo</a>
        <a href="{{ route('professor.alunos.index', ($primeiroVinculo?->turma_id ? ['turma_id' => $primeiroVinculo->turma_id] : [])) }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Ver alunos</a>
        <a href="{{ route('professor.turmas.index') }}" class="bg-white text-indigo-800 hover:bg-indigo-50 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Ver todas as turmas</a>
    </div>
</div>
