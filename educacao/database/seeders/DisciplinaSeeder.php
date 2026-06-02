<?php

namespace Database\Seeders;

use App\Models\Academico\Disciplina;
use Illuminate\Database\Seeder;

class DisciplinaSeeder extends Seeder
{
    public function run(): void
    {
        $disciplinas = [
            ['nome' => 'Língua Portuguesa', 'sigla' => 'LP', 'carga_horaria' => 200],
            ['nome' => 'Matemática', 'sigla' => 'MAT', 'carga_horaria' => 200],
            ['nome' => 'Ciências', 'sigla' => 'CIE', 'carga_horaria' => 120],
            ['nome' => 'História', 'sigla' => 'HIS', 'carga_horaria' => 120],
            ['nome' => 'Geografia', 'sigla' => 'GEO', 'carga_horaria' => 120],
            ['nome' => 'Educação Física', 'sigla' => 'EDF', 'carga_horaria' => 80],
            ['nome' => 'Arte', 'sigla' => 'ART', 'carga_horaria' => 80],
            ['nome' => 'Inglês', 'sigla' => 'ING', 'carga_horaria' => 80],
            ['nome' => 'Ensino Religioso', 'sigla' => 'ER', 'carga_horaria' => 40],
            ['nome' => 'Física', 'sigla' => 'FIS', 'carga_horaria' => 120],
            ['nome' => 'Química', 'sigla' => 'QUI', 'carga_horaria' => 120],
            ['nome' => 'Biologia', 'sigla' => 'BIO', 'carga_horaria' => 120],
        ];

        foreach ($disciplinas as $d) {
            Disciplina::firstOrCreate(
                ['sigla' => $d['sigla']],
                array_merge($d, ['municipio_id' => null, 'ativo' => true])
            );
        }
    }
}
