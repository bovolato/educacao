<?php

namespace Database\Seeders\Concerns;

use App\Models\User;

/**
 * Helpers de geração de dados pessoais fake para seeders.
 * Após a unificação, os dados comuns ficam na própria tabela `users`.
 */
trait GeraPessoaFake
{
    protected array $nomesM = [
        'Carlos Alberto', 'João Pedro', 'Lucas Felipe', 'Gabriel Santos', 'Rafael Oliveira',
        'Matheus Costa', 'Bruno Ferreira', 'Diego Souza', 'Felipe Lima', 'Rodrigo Pereira',
        'Eduardo Alves', 'André Martins', 'Pedro Henrique', 'Leonardo Silva', 'Thiago Rocha',
        'Vinicius Carvalho', 'Guilherme Mendes', 'Igor Barbosa', 'Daniel Araujo', 'Marco Antônio',
    ];

    protected array $nomesF = [
        'Ana Beatriz', 'Maria Clara', 'Júlia Fernanda', 'Larissa Rodrigues', 'Fernanda Castro',
        'Camila Ribeiro', 'Isabela Pinto', 'Gabriela Moreira', 'Patricia Lima', 'Amanda Costa',
        'Bruna Oliveira', 'Mariana Santos', 'Caroline Ferreira', 'Vanessa Almeida', 'Thaís Cunha',
        'Letícia Gomes', 'Priscila Cardoso', 'Renata Vieira', 'Sabrina Nunes', 'Tatiana Melo',
    ];

    protected array $sobrenomes = [
        'Silva', 'Santos', 'Oliveira', 'Souza', 'Lima', 'Pereira', 'Ferreira', 'Costa',
        'Rodrigues', 'Alves', 'Martins', 'Carvalho', 'Ribeiro', 'Rocha', 'Mendes',
        'Araújo', 'Barbosa', 'Castro', 'Pinto', 'Moreira',
    ];

    protected array $bairros = [
        'Centro', 'Jardim das Flores', 'Vila Nova', 'Bela Vista', 'Parque Industrial',
        'Jardim América', 'Santa Helena', 'Boa Esperança', 'Alto da Serra', 'Vila São José',
    ];

    protected array $logradouros = [
        'Rua das Flores', 'Avenida Brasil', 'Rua 7 de Setembro', 'Rua Tiradentes',
        'Avenida Paulista', 'Rua Santa Cruz', 'Rua João Pessoa', 'Avenida Getúlio Vargas',
        'Rua dos Pinheiros', 'Rua da Paz',
    ];

    protected function primeiroNome(string $sexo): string
    {
        return $sexo === 'M'
            ? $this->nomesM[array_rand($this->nomesM)]
            : $this->nomesF[array_rand($this->nomesF)];
    }

    protected function nomeCompleto(string $sexo): string
    {
        return $this->primeiroNome($sexo)
            . ' ' . $this->sobrenomes[array_rand($this->sobrenomes)]
            . ' ' . $this->sobrenomes[array_rand($this->sobrenomes)];
    }

    protected function dataNascimento(int $anoMin, int $anoMax): string
    {
        return sprintf('%04d-%02d-%02d', rand($anoMin, $anoMax), rand(1, 12), rand(1, 28));
    }

    /** Cria contato celular + email + endereço principal para o usuário informado. */
    protected function criarContatosEndereco(User $user, string $emailLocal, int $seq): void
    {
        $user->contatos()->create([
            'tipo'      => 'celular',
            'valor'     => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
            'principal' => true,
        ]);

        $user->contatos()->create([
            'tipo'      => 'email',
            'valor'     => $emailLocal . '@email.com',
            'principal' => false,
        ]);

        $user->enderecos()->create([
            'logradouro' => $this->logradouros[array_rand($this->logradouros)],
            'numero'     => (string) rand(10, 2000),
            'bairro'     => $this->bairros[array_rand($this->bairros)],
            'cidade'     => 'Município Exemplo',
            'uf'         => 'SP',
            'cep'        => rand(10000, 99999) . '-' . str_pad((string) ($seq % 1000), 3, '0', STR_PAD_LEFT),
            'principal'  => true,
        ]);
    }

    protected function gerarCpf(): string
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

        return sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', ...$n);
    }
}
