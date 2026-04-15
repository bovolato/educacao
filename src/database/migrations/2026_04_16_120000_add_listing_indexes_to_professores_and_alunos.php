<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->index(['ativo', 'id'], 'professores_ativo_id_index');
        });

        Schema::table('alunos', function (Blueprint $table) {
            $table->index(['ativo', 'id'], 'alunos_ativo_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->dropIndex('professores_ativo_id_index');
        });

        Schema::table('alunos', function (Blueprint $table) {
            $table->dropIndex('alunos_ativo_id_index');
        });
    }
};
