<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{EscolaController, AnoLetivoController, SerieController, DisciplinaController, TurnoController, UsuarioController, MunicipioController, SalaController};
use App\Http\Controllers\Pessoas\{AlunoController, ProfessorController, ResponsavelController};
use App\Http\Controllers\Academico\{TurmaController, MatriculaController};
use App\Http\Controllers\Comunicacao\AvisoController;
use App\Http\Controllers\Escola\{DocumentoEscolaController, FrequenciaEscolaController, NotaEscolaController};
use App\Http\Controllers\Professor\{
    AlunosProfessorController,
    AnotacoesProfessorController,
    AvaliacoesProfessorController,
    AulasProfessorController,
    ContextoProfessorController,
    FrequenciasBimestreProfessorController,
    FrequenciasProfessorController,
    MateriaisProfessorController,
    NotasBimestreProfessorController,
    NotasProfessorController,
    PlanosAulaProfessorController,
    PlanosEnsinoProfessorController,
    TarefasProfessorController,
    TurmasProfessorController,
};
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
        Route::get('professores/{professor}/usuario', [ProfessorController::class, 'usuarioForm'])->name('professores.usuario.form');
        Route::post('professores/{professor}/usuario', [ProfessorController::class, 'usuarioStore'])->name('professores.usuario.store');
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
            $eid = app(\App\Services\EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola(auth()->user());
            if ($eid !== null && (int) $escola->id !== $eid) {
                abort(403);
            }

            return response()->json(
                \App\Models\Institucional\Sala::where('escola_id', $escola->id)
                    ->where('ativo', true)
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'codigo', 'capacidade'])
            );
        })->name('escola.salas');

        Route::get('/escolas/{escola}/turmas', function (\App\Models\Institucional\Escola $escola) {
            $eid = app(\App\Services\EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola(auth()->user());
            if ($eid !== null && (int) $escola->id !== $eid) {
                abort(403);
            }

            return response()->json(
                \App\Models\Academico\Turma::where('escola_id', $escola->id)
                    ->where('status', 'ativa')
                    ->with(['serie:id,nome', 'turno:id,nome'])
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'serie_id', 'turno_id', 'capacidade'])
            );
        })->name('escola.turmas');

        Route::get('/escolas/{escola}/professores', function (\App\Models\Institucional\Escola $escola) {
            $eid = app(\App\Services\EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola(auth()->user());
            if ($eid !== null && (int) $escola->id !== $eid) {
                abort(403);
            }

            return response()->json(
                \App\Models\Pessoas\Professor::with('pessoa')
                    ->where('escola_id', $escola->id)
                    ->where('ativo', true)
                    ->get()
                    ->map(fn($p) => ['id' => $p->id, 'nome' => $p->pessoa?->nome ?? ''])
            );
        })->name('escola.professores');

        Route::get('/turmas/{turma}/disciplinas', function (\App\Models\Academico\Turma $turma) {
            if (! app(\App\Services\EscopoAcesso::class)->turmaAcessivelPeloUsuario(auth()->user(), $turma)) {
                abort(403);
            }

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
    Route::get('/escola/frequencias', [FrequenciaEscolaController::class, 'index'])->name('escola.frequencias.index');
    Route::get('/escola/frequencias/turmas/{turma}', [FrequenciaEscolaController::class, 'turma'])->name('escola.frequencias.turma');
    Route::get('/escola/frequencias/aulas/{aula}', [FrequenciaEscolaController::class, 'aula'])->name('escola.frequencias.aula');

    Route::get('/escola/notas', [NotaEscolaController::class, 'index'])->name('escola.notas.index');
    Route::get('/escola/notas/turmas/{turma}', [NotaEscolaController::class, 'turma'])->name('escola.notas.turma');
    Route::get('/escola/notas/avaliacoes/{avaliacao}', [NotaEscolaController::class, 'avaliacao'])->name('escola.notas.avaliacao');

    Route::get('/escola/documentos', [DocumentoEscolaController::class, 'index'])->name('escola.documentos.index');
    Route::get('/escola/documentos/novo', [DocumentoEscolaController::class, 'create'])->name('escola.documentos.create');
    Route::post('/escola/documentos', [DocumentoEscolaController::class, 'store'])->name('escola.documentos.store');
    Route::get('/escola/documentos/{documentoEmitido}/imprimir', [DocumentoEscolaController::class, 'imprimir'])->name('escola.documentos.imprimir');

    Route::middleware(['professor'])->prefix('professor')->name('professor.')->group(function () {
        Route::post('contexto/periodo', [ContextoProfessorController::class, 'setPeriodo'])->name('contexto.periodo');

        Route::get('turmas', [TurmasProfessorController::class, 'index'])->name('turmas.index');

        Route::get('frequencias', [FrequenciasBimestreProfessorController::class, 'index'])->name('frequencias.index');
        Route::get('frequencias/create', [FrequenciasBimestreProfessorController::class, 'create'])->name('frequencias.create');
        Route::post('frequencias', [FrequenciasBimestreProfessorController::class, 'store'])->name('frequencias.store');
        Route::get('frequencias/{frequenciaBimestre}/edit', [FrequenciasBimestreProfessorController::class, 'edit'])->name('frequencias.edit');
        Route::put('frequencias/{frequenciaBimestre}', [FrequenciasBimestreProfessorController::class, 'update'])->name('frequencias.update');

        // Legado (por aula)
        Route::get('frequencias-por-aula', [FrequenciasProfessorController::class, 'index'])->name('frequencias.legado.index');
        Route::get('frequencias/aulas/{aula}/edit', [FrequenciasProfessorController::class, 'edit'])->name('frequencias.aula.edit');
        Route::put('frequencias/aulas/{aula}', [FrequenciasProfessorController::class, 'update'])->name('frequencias.aula.update');

        Route::get('notas', [NotasProfessorController::class, 'index'])->name('notas.index');
        Route::get('notas/avaliacoes/{avaliacao}/lancar', [NotasProfessorController::class, 'lancar'])->name('notas.lancar');
        Route::post('notas/avaliacoes/{avaliacao}', [NotasProfessorController::class, 'salvar'])->name('notas.salvar');

        Route::get('notas-bimestre', [NotasBimestreProfessorController::class, 'index'])->name('notas-bimestre.index');
        Route::get('notas-bimestre/create', [NotasBimestreProfessorController::class, 'create'])->name('notas-bimestre.create');
        Route::post('notas-bimestre', [NotasBimestreProfessorController::class, 'store'])->name('notas-bimestre.store');
        Route::get('notas-bimestre/{notaBimestre}/edit', [NotasBimestreProfessorController::class, 'edit'])->name('notas-bimestre.edit');
        Route::put('notas-bimestre/{notaBimestre}', [NotasBimestreProfessorController::class, 'update'])->name('notas-bimestre.update');

        Route::get('avaliacoes/{avaliacao}', [AvaliacoesProfessorController::class, 'show'])
            ->whereNumber('avaliacao')
            ->name('avaliacoes.show');
        Route::resource('avaliacoes', AvaliacoesProfessorController::class)
            ->except(['show'])
            ->parameters(['avaliacoes' => 'avaliacao']);

        Route::get('aulas', [AulasProfessorController::class, 'index'])->name('aulas.index');
        Route::get('aulas/create', [AulasProfessorController::class, 'create'])->name('aulas.create');
        Route::post('aulas', [AulasProfessorController::class, 'store'])->name('aulas.store');
        Route::get('aulas/{aula}/conteudo', [AulasProfessorController::class, 'conteudo'])->name('aulas.conteudo');
        Route::post('aulas/{aula}/conteudo', [AulasProfessorController::class, 'salvarConteudo'])->name('aulas.conteudo.salvar');

        Route::resource('planos-ensino', PlanosEnsinoProfessorController::class)
            ->except(['show'])
            ->parameters(['planos-ensino' => 'planoEnsino']);
        Route::resource('planos', PlanosAulaProfessorController::class)
            ->except(['show'])
            ->parameters(['planos' => 'planoAula']);
        Route::resource('materiais', MateriaisProfessorController::class)
            ->parameters(['materiais' => 'materialDidatico']);
        Route::get('materiais/{materialDidatico}/download', [MateriaisProfessorController::class, 'download'])->name('materiais.download');
        Route::resource('tarefas', TarefasProfessorController::class)->except(['show']);

        Route::get('alunos', [AlunosProfessorController::class, 'index'])->name('alunos.index');
        Route::get('alunos/matriculas/{matricula}', [AlunosProfessorController::class, 'show'])->name('alunos.show');
        Route::post('alunos/matriculas/{matricula}/media-manual', [AlunosProfessorController::class, 'salvarMediaManual'])->name('alunos.media-manual.salvar');
        Route::post('alunos/matriculas/{matricula}/tarefas', [AlunosProfessorController::class, 'salvarTarefaRegistro'])->name('alunos.tarefas.salvar');

        Route::resource('anotacoes', AnotacoesProfessorController::class)
            ->parameters(['anotacoes' => 'anotacaoProfessor']);
    });

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
