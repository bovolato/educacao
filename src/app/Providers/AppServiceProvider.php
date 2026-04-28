<?php

namespace App\Providers;

use App\Console\Commands\AuditRouteReferencesCommand;
use App\Models\Academico\Matricula;
use App\Models\Academico\Turma;
use App\Models\Pessoas\Aluno;
use App\Models\Pessoas\Professor;
use App\Policies\AlunoPolicy;
use App\Policies\MatriculaPolicy;
use App\Policies\ProfessorPolicy;
use App\Policies\TurmaPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands([
            AuditRouteReferencesCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::policy(Aluno::class, AlunoPolicy::class);
        Gate::policy(Professor::class, ProfessorPolicy::class);
        Gate::policy(Turma::class, TurmaPolicy::class);
        Gate::policy(Matricula::class, MatriculaPolicy::class);
    }
}
