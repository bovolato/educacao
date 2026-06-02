<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institucional\Municipio;
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

        $admin = User::create([
            'tipo'         => 'admin',
            'municipio_id' => $municipio->id,
            'nome'         => 'Administrador do Sistema',
            'email'        => 'admin@sigem.edu.br',
            'username'     => 'admin',
            'password'     => Hash::make('admin@2026'),
            'ativo'        => true,
        ]);

        $admin->assignRole('super_admin');

        $this->command->info('Usuário admin criado: admin@sigem.edu.br / admin@2026');
    }
}
