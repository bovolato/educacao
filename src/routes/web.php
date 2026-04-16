<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{EscolaController, AnoLetivoController, SerieController, DisciplinaController, TurnoController, UsuarioController, MunicipioController, SalaController};
use App\Http\Controllers\Pessoas\{AlunoController, ProfessorController, ResponsavelController};
use App\Http\Controllers\Academico\{TurmaController, MatriculaController};
use App\Http\Controllers\Comunicacao\AvisoController;
use Illuminate\Support\Facades\Route;

// Redirecionar raiz para dashboard se autenticado, senão para login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Dashboard principal
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil do usuário
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================================
// Rotas autenticadas
// ============================================================
Route::middleware('auth')->group(function () {

    // ----------------------------------------------------------
    // Módulo Administração
    // ----------------------------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('municipios', MunicipioController::class)->names('municipios');
        Route::resource('escolas', EscolaController::class)->names('escolas');
        Route::resource('escolas.salas', SalaController::class)->names('escolas.salas')->shallow();
        Route::resource('anos-letivos', AnoLetivoController::class)->names('anos-letivos');
        Route::resource('series', SerieController::class)->names('series');
        Route::resource('disciplinas', DisciplinaController::class)->names('disciplinas');
        Route::resource('turnos', TurnoController::class)->names('turnos');
        Route::resource('usuarios', UsuarioController::class)->names('usuarios');
    });

    // ----------------------------------------------------------
    // Módulo Pessoas
    // ----------------------------------------------------------
    Route::prefix('pessoas')->name('pessoas.')->group(function () {
        Route::resource('alunos', AlunoController::class)->names('alunos');
        // Força {professor} na URI (Str::singular('professores') vira "professore" e quebra rotas que usam {professor})
        Route::resource('professores', ProfessorController::class)
            ->names('professores')
            ->parameters(['professores' => 'professor']);
        Route::get('professores/{professor}/vincular-turmas', [ProfessorController::class, 'vincularTurmas'])->name('professores.vincular-turmas');
        Route::post('professores/{professor}/vincular-turmas', [ProfessorController::class, 'salvarVinculoTurmas'])->name('professores.salvar-vinculo-turmas');
        Route::resource('responsaveis', ResponsavelController::class)->names('responsaveis');
    });

    // ----------------------------------------------------------
    // Módulo Acadêmico
    // ----------------------------------------------------------
    Route::prefix('academico')->name('academico.')->group(function () {
        Route::resource('turmas', TurmaController::class)->names('turmas');
        Route::resource('matriculas', MatriculaController::class)->names('matriculas');
    });

    // ----------------------------------------------------------
    // API interna (JSON) — usada por selects dinâmicos
    // ----------------------------------------------------------
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/municipios/{municipio}/escolas', function (\App\Models\Institucional\Municipio $municipio) {
            return response()->json(
                $municipio->escolas()->where('status', 'ativa')->orderBy('nome')->get(['id', 'nome'])
            );
        })->name('municipio.escolas');

        Route::get('/escolas/{escola}/salas', function (\App\Models\Institucional\Escola $escola) {
            return response()->json(
                \App\Models\Institucional\Sala::where('escola_id', $escola->id)
                    ->where('ativo', true)
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'codigo', 'capacidade'])
            );
        })->name('escola.salas');

        Route::get('/escolas/{escola}/turmas', function (\App\Models\Institucional\Escola $escola) {
            return response()->json(
                \App\Models\Academico\Turma::where('escola_id', $escola->id)
                    ->where('status', 'ativa')
                    ->with(['serie:id,nome', 'turno:id,nome'])
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'serie_id', 'turno_id', 'capacidade'])
            );
        })->name('escola.turmas');

        Route::get('/escolas/{escola}/professores', function (\App\Models\Institucional\Escola $escola) {
            return response()->json(
                \App\Models\Pessoas\Professor::with('pessoa')
                    ->where('escola_id', $escola->id)
                    ->where('ativo', true)
                    ->get()
                    ->map(fn($p) => ['id' => $p->id, 'nome' => $p->pessoa?->nome ?? ''])
            );
        })->name('escola.professores');

        Route::get('/turmas/{turma}/disciplinas', function (\App\Models\Academico\Turma $turma) {
            return response()->json(
                $turma->disciplinas()->orderBy('nome')->get(['disciplinas.id', 'disciplinas.nome', 'disciplinas.sigla'])
            );
        })->name('turma.disciplinas');
    });

    // ----------------------------------------------------------
    // Módulo Comunicação
    // ----------------------------------------------------------
    Route::resource('avisos', AvisoController::class)->names('avisos');

    // ----------------------------------------------------------
    // Rotas de contexto escolar (stubs)
    // ----------------------------------------------------------
    Route::get('/secretaria/alunos', fn() => redirect()->route('pessoas.alunos.index'))->name('secretaria.alunos.index');
    Route::get('/secretaria/professores', fn() => redirect()->route('pessoas.professores.index'))->name('secretaria.professores.index');
    Route::get('/secretaria/matriculas', fn() => redirect()->route('academico.matriculas.index'))->name('secretaria.matriculas.index');
    Route::get('/secretaria/escolas', fn() => redirect()->route('admin.escolas.index'))->name('secretaria.escolas.index');

    Route::get('/escola/turmas', fn() => redirect()->route('academico.turmas.index'))->name('escola.turmas.index');
    Route::get('/escola/alunos', fn() => redirect()->route('pessoas.alunos.index'))->name('escola.alunos.index');
    Route::get('/escola/professores', fn() => redirect()->route('pessoas.professores.index'))->name('escola.professores.index');
    Route::get('/escola/matriculas', fn() => redirect()->route('academico.matriculas.index'))->name('escola.matriculas.index');
    Route::get('/escola/frequencias', fn() => view('em-construcao', ['titulo' => 'Frequência']))->name('escola.frequencias.index');
    Route::get('/escola/notas', fn() => view('em-construcao', ['titulo' => 'Notas']))->name('escola.notas.index');
    Route::get('/escola/documentos', fn() => view('em-construcao', ['titulo' => 'Documentos']))->name('escola.documentos.index');

    Route::get('/professor/turmas', fn() => view('em-construcao', ['titulo' => 'Minhas Turmas']))->name('professor.turmas.index');
    Route::get('/professor/frequencias', fn() => view('em-construcao', ['titulo' => 'Frequência']))->name('professor.frequencias.index');
    Route::get('/professor/notas', fn() => view('em-construcao', ['titulo' => 'Lançar Notas']))->name('professor.notas.index');
    Route::get('/professor/avaliacoes', fn() => view('em-construcao', ['titulo' => 'Avaliações']))->name('professor.avaliacoes.index');
    Route::get('/professor/aulas', fn() => view('em-construcao', ['titulo' => 'Conteúdo Ministrado']))->name('professor.aulas.index');
    Route::get('/professor/planos', fn() => view('em-construcao', ['titulo' => 'Planos de Aula']))->name('professor.planos.index');
    Route::get('/professor/materiais', fn() => view('em-construcao', ['titulo' => 'Materiais Didáticos']))->name('professor.materiais.index');
    Route::get('/professor/tarefas', fn() => view('em-construcao', ['titulo' => 'Tarefas']))->name('professor.tarefas.index');

    Route::get('/relatorios', fn() => view('em-construcao', ['titulo' => 'Relatórios']))->name('relatorios.index');

    Route::get('/portal/notas', fn() => view('em-construcao', ['titulo' => 'Minhas Notas']))->name('portal.notas');
    Route::get('/portal/frequencia', fn() => view('em-construcao', ['titulo' => 'Minha Frequência']))->name('portal.frequencia');
    Route::get('/portal/boletim', fn() => view('em-construcao', ['titulo' => 'Meu Boletim']))->name('portal.boletim');
    Route::get('/portal/aulas', fn() => view('em-construcao', ['titulo' => 'Aulas e Conteúdos']))->name('portal.aulas');
    Route::get('/portal/tarefas', fn() => view('em-construcao', ['titulo' => 'Tarefas']))->name('portal.tarefas');
    Route::get('/portal/materiais', fn() => view('em-construcao', ['titulo' => 'Materiais']))->name('portal.materiais');
    Route::get('/portal/avisos', fn() => redirect()->route('avisos.index'))->name('portal.avisos');
    Route::get('/portal/documentos', fn() => view('em-construcao', ['titulo' => 'Documentos']))->name('portal.documentos');
});

require __DIR__.'/auth.php';
