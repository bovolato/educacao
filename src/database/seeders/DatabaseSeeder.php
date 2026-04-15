<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Institucional (município, escola, etapas, séries, turnos, salas)
            MunicipioSeeder::class,

            // 2. Permissões e perfis
            PermissaoSeeder::class,

            // 3. Administrador do sistema
            AdminSeeder::class,

            // 4. Disciplinas do município
            DisciplinaSeeder::class,

            // 5. Pessoas (dados pessoais base)
            PessoaSeeder::class,

            // 6. Alunos (vinculados a pessoas jovens)
            AlunoSeeder::class,

            // 7. Professores (com usuários no sistema)
            ProfessorSeeder::class,

            // 8. Funcionários e gestores (com usuários)
            FuncionarioSeeder::class,

            // 9. Responsáveis (vinculados aos alunos)
            ResponsavelSeeder::class,

            // 10. Turmas (vinculadas a escola/ano/série/turno)
            TurmaSeeder::class,

            // 11. Matrículas (alunos nas turmas)
            MatriculaSeeder::class,

            // 12. Aulas e frequências
            AulaSeeder::class,

            // 13. Avaliações e notas
            AvaliacaoNotaSeeder::class,

            // 14. Avisos
            AvisoSeeder::class,
        ]);
    }
}
