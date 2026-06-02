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

            // 5. Alunos (usuários tipo aluno + dados complementares)
            AlunoSeeder::class,

            // 6. Professores (usuários tipo professor, com login)
            ProfessorSeeder::class,

            // 7. Funcionários e gestores (usuários tipo funcionario/gestor)
            FuncionarioSeeder::class,

            // 8. Responsáveis (usuários tipo responsavel, vinculados aos alunos)
            ResponsavelSeeder::class,

            // 9. Turmas (vinculadas a escola/ano/série/turno)
            TurmaSeeder::class,

            // 10. Matrículas (alunos nas turmas)
            MatriculaSeeder::class,

            // 11. Aulas e frequências
            AulaSeeder::class,

            // 12. Avaliações e notas
            AvaliacaoNotaSeeder::class,

            // 13. Avisos
            AvisoSeeder::class,
        ]);
    }
}
