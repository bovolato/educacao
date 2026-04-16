<?php

namespace Database\Seeders;

use App\Models\Pessoas\{Pessoa, Professor, PessoaContato, PessoaEndereco};
use App\Models\Institucional\Escola;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfessorSeeder extends Seeder
{
    private array $formacoes = [
        'Licenciatura em Letras',
        'Licenciatura em Matemática',
        'Licenciatura em Pedagogia',
        'Licenciatura em História',
        'Licenciatura em Geografia',
        'Licenciatura em Educação Física',
        'Licenciatura em Arte',
        'Licenciatura em Ciências Biológicas',
        'Licenciatura em Física',
        'Licenciatura em Química',
    ];

    private array $nomesProf = [
        ['nome' => 'Ana Paula Vieira', 'sexo' => 'F'],
        ['nome' => 'Carlos Eduardo Mota', 'sexo' => 'M'],
        ['nome' => 'Sandra Regina Costa', 'sexo' => 'F'],
        ['nome' => 'Roberto Alves Junior', 'sexo' => 'M'],
        ['nome' => 'Márcia Fernanda Lima', 'sexo' => 'F'],
        ['nome' => 'Paulo Sérgio Dias', 'sexo' => 'M'],
        ['nome' => 'Fernanda Cristina Souza', 'sexo' => 'F'],
        ['nome' => 'Marcos Antônio Pereira', 'sexo' => 'M'],
        ['nome' => 'Claudia Helena Santos', 'sexo' => 'F'],
        ['nome' => 'José Ricardo Barbosa', 'sexo' => 'M'],
        ['nome' => 'Luciana Aparecida Rocha', 'sexo' => 'F'],
        ['nome' => 'Fabio Henrique Mendes', 'sexo' => 'M'],
        ['nome' => 'Rosana Cristina Gomes', 'sexo' => 'F'],
        ['nome' => 'Wellington Ferreira', 'sexo' => 'M'],
        ['nome' => 'Tatiane Oliveira Cruz', 'sexo' => 'F'],
    ];

    public function run(): void
    {
        $municipioId = \App\Models\Institucional\Municipio::first()->id;
        $escola      = Escola::first();
        $escolaId    = $escola?->id;

        foreach ($this->nomesProf as $i => $dadosProf) {
            $pessoa = Pessoa::create([
                'nome'            => $dadosProf['nome'],
                'cpf'             => $this->gerarCpf(),
                'data_nascimento' => sprintf('%04d-%02d-%02d', rand(1975, 1990), rand(1, 12), rand(1, 28)),
                'sexo'            => $dadosProf['sexo'],
                'naturalidade'    => 'Município Exemplo',
                'naturalidade_uf' => 'SP',
                'ativo'           => true,
            ]);

            PessoaContato::create([
                'pessoa_id' => $pessoa->id,
                'tipo'      => 'celular',
                'valor'     => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'principal' => true,
            ]);

            $emailSlug = strtolower(explode(' ', $dadosProf['nome'])[0]) . ($i + 1);
            PessoaContato::create([
                'pessoa_id' => $pessoa->id,
                'tipo'      => 'email',
                'valor'     => $emailSlug . '@escola.edu.br',
                'principal' => false,
            ]);

            PessoaEndereco::create([
                'pessoa_id'  => $pessoa->id,
                'logradouro' => 'Rua dos Professores',
                'numero'     => (string) (($i + 1) * 10),
                'bairro'     => 'Centro',
                'cidade'     => 'Município Exemplo',
                'uf'         => 'SP',
                'cep'        => '01234-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'principal'  => true,
            ]);

            $professor = Professor::create([
                'pessoa_id'             => $pessoa->id,
                'escola_id'             => $escolaId,
                'cidade_vinculo'        => $escola?->cidade,
                'matricula_funcional'   => 'PROF' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'formacao'              => $this->formacoes[$i % count($this->formacoes)],
                'registro_profissional' => 'REG' . rand(10000, 99999),
                'data_admissao'         => sprintf('%04d-%02d-%02d', rand(2000, 2022), rand(1, 12), rand(1, 28)),
                'ativo'                 => true,
            ]);

            // Criar usuário para o professor
            $user = User::create([
                'pessoa_id'    => $pessoa->id,
                'municipio_id' => $municipioId,
                'name'         => $dadosProf['nome'],
                'email'        => $emailSlug . '@escola.edu.br',
                'username'     => $emailSlug,
                'password'     => Hash::make('professor@2026'),
                'ativo'        => true,
            ]);
            $user->assignRole('professor');
        }
    }

    private function gerarCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) $n[] = rand(0, 9);
        $d1 = 0;
        for ($i = 0; $i < 9; $i++) $d1 += $n[$i] * (10 - $i);
        $d1 = ($d1 % 11 < 2) ? 0 : 11 - ($d1 % 11);
        $n[] = $d1;
        $d2 = 0;
        for ($i = 0; $i < 10; $i++) $d2 += $n[$i] * (11 - $i);
        $d2 = ($d2 % 11 < 2) ? 0 : 11 - ($d2 % 11);
        $n[] = $d2;
        return sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', ...$n);
    }
}
