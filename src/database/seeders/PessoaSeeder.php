<?php

namespace Database\Seeders;

use App\Models\Pessoas\{Pessoa, PessoaContato, PessoaEndereco};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PessoaSeeder extends Seeder
{
    private array $nomesM = [
        'Carlos Alberto', 'João Pedro', 'Lucas Felipe', 'Gabriel Santos', 'Rafael Oliveira',
        'Matheus Costa', 'Bruno Ferreira', 'Diego Souza', 'Felipe Lima', 'Rodrigo Pereira',
        'Eduardo Alves', 'André Martins', 'Pedro Henrique', 'Leonardo Silva', 'Thiago Rocha',
        'Vinicius Carvalho', 'Guilherme Mendes', 'Igor Barbosa', 'Daniel Araujo', 'Marco Antônio',
    ];

    private array $nomesF = [
        'Ana Beatriz', 'Maria Clara', 'Júlia Fernanda', 'Larissa Rodrigues', 'Fernanda Castro',
        'Camila Ribeiro', 'Isabela Pinto', 'Gabriela Moreira', 'Patricia Lima', 'Amanda Costa',
        'Bruna Oliveira', 'Mariana Santos', 'Caroline Ferreira', 'Vanessa Almeida', 'Thaís Cunha',
        'Letícia Gomes', 'Priscila Cardoso', 'Renata Vieira', 'Sabrina Nunes', 'Tatiana Melo',
    ];

    private array $sobrenomes = [
        'Silva', 'Santos', 'Oliveira', 'Souza', 'Lima', 'Pereira', 'Ferreira', 'Costa',
        'Rodrigues', 'Alves', 'Martins', 'Carvalho', 'Ribeiro', 'Rocha', 'Mendes',
        'Araújo', 'Barbosa', 'Castro', 'Pinto', 'Moreira',
    ];

    private array $bairros = [
        'Centro', 'Jardim das Flores', 'Vila Nova', 'Bela Vista', 'Parque Industrial',
        'Jardim América', 'Santa Helena', 'Boa Esperança', 'Alto da Serra', 'Vila São José',
    ];

    private array $logradouros = [
        'Rua das Flores', 'Avenida Brasil', 'Rua 7 de Setembro', 'Rua Tiradentes',
        'Avenida Paulista', 'Rua Santa Cruz', 'Rua João Pessoa', 'Avenida Getúlio Vargas',
        'Rua dos Pinheiros', 'Rua da Paz',
    ];

    public function run(): void
    {
        // Gerar 80 pessoas
        for ($i = 0; $i < 80; $i++) {
            $sexo = $i % 2 === 0 ? 'M' : 'F';

            if ($sexo === 'M') {
                $primeiroNome = $this->nomesM[array_rand($this->nomesM)];
            } else {
                $primeiroNome = $this->nomesF[array_rand($this->nomesF)];
            }

            $sobrenome  = $this->sobrenomes[array_rand($this->sobrenomes)];
            $sobrenome2 = $this->sobrenomes[array_rand($this->sobrenomes)];
            $nome       = $primeiroNome . ' ' . $sobrenome . ' ' . $sobrenome2;

            $ano  = rand(1970, 2020);
            $mes  = rand(1, 12);
            $dia  = rand(1, 28);
            $nasc = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);

            $cpf = $this->gerarCpf();

            $pessoa = Pessoa::create([
                'nome'            => $nome,
                'cpf'             => $cpf,
                'data_nascimento' => $nasc,
                'sexo'            => $sexo,
                'nome_mae'        => $this->nomesF[array_rand($this->nomesF)] . ' ' . $this->sobrenomes[array_rand($this->sobrenomes)],
                'nome_pai'        => $this->nomesM[array_rand($this->nomesM)] . ' ' . $this->sobrenomes[array_rand($this->sobrenomes)],
                'naturalidade'    => 'Município Exemplo',
                'naturalidade_uf' => 'SP',
                'nacionalidade'   => 'Brasileira',
                'ativo'           => true,
            ]);

            // Contato celular
            PessoaContato::create([
                'pessoa_id' => $pessoa->id,
                'tipo'      => 'celular',
                'valor'     => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'principal' => true,
            ]);

            // E-mail
            $emailLocal = strtolower(explode(' ', $primeiroNome)[0]) . rand(10, 99);
            PessoaContato::create([
                'pessoa_id' => $pessoa->id,
                'tipo'      => 'email',
                'valor'     => $emailLocal . '@email.com',
                'principal' => false,
            ]);

            // Endereço
            PessoaEndereco::create([
                'pessoa_id'  => $pessoa->id,
                'logradouro' => $this->logradouros[array_rand($this->logradouros)],
                'numero'     => (string) rand(10, 2000),
                'bairro'     => $this->bairros[array_rand($this->bairros)],
                'cidade'     => 'Município Exemplo',
                'uf'         => 'SP',
                'cep'        => rand(10000, 99999) . '-' . rand(100, 999),
                'principal'  => true,
            ]);
        }
    }

    private function gerarCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[] = rand(0, 9);
        }

        $d1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $d1 += $n[$i] * (10 - $i);
        }
        $d1 = ($d1 % 11 < 2) ? 0 : 11 - ($d1 % 11);
        $n[] = $d1;

        $d2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $d2 += $n[$i] * (11 - $i);
        }
        $d2 = ($d2 % 11 < 2) ? 0 : 11 - ($d2 % 11);
        $n[] = $d2;

        return sprintf(
            '%d%d%d.%d%d%d.%d%d%d-%d%d',
            $n[0], $n[1], $n[2], $n[3], $n[4], $n[5], $n[6], $n[7], $n[8], $n[9], $n[10]
        );
    }
}
