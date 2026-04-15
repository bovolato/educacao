<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institucional\Municipio;
use App\Models\Pessoas\Pessoa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $municipio = Municipio::first();
        if (! $municipio) {
            return;
        }

        $pessoa = Pessoa::create([
            'nome'  => 'Administrador do Sistema',
            'cpf'   => null,
            'ativo' => true,
        ]);

        $admin = User::create([
            'pessoa_id'    => $pessoa->id,
            'municipio_id' => $municipio->id,
            'name'         => 'Administrador',
            'email'        => 'admin@sigem.edu.br',
            'username'     => 'admin',
            'password'     => Hash::make('admin@2026'),
            'ativo'        => true,
        ]);

        $admin->assignRole('super_admin');

        $this->command->info('Usuário admin criado: admin@sigem.edu.br / admin@2026');
    }
}
