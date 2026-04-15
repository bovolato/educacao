<?php

namespace Database\Seeders;

use App\Models\Pessoas\{Pessoa, Aluno};
use Illuminate\Database\Seeder;

class AlunoSeeder extends Seeder
{
    public function run(): void
    {
        // Pegar pessoas nascidas entre 2005 e 2020 (faixa etária escolar)
        $pessoas = Pessoa::whereRaw("YEAR(data_nascimento) BETWEEN 2005 AND 2020")
            ->doesntHave('aluno')
            ->limit(60)
            ->get();

        // Se não tiver suficiente, pegar qualquer pessoa sem aluno
        if ($pessoas->count() < 30) {
            $pessoas = Pessoa::doesntHave('aluno')->limit(60)->get();
        }

        foreach ($pessoas as $i => $pessoa) {
            Aluno::create([
                'pessoa_id'       => $pessoa->id,
                'ra'              => 'RA' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'codigo_aluno'    => 'AL' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'nis'             => rand(10000000000, 99999999999),
                'necessidades_especiais' => $i % 15 === 0,
                'descricao_necessidades' => $i % 15 === 0 ? 'TDAH leve' : null,
                'usa_transporte'  => $i % 4 === 0,
                'ativo'           => true,
            ]);
        }
    }
}
