<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->string('cidade_vinculo', 120)->nullable()->after('pessoa_id');
        });

        Schema::table('professores', function (Blueprint $table) {
            $table->string('cidade_vinculo', 120)->nullable()->after('escola_id');
        });

        if (Schema::hasTable('escolas') && Schema::hasTable('professores')) {
            DB::statement('
                UPDATE professores AS p
                INNER JOIN escolas AS e ON e.id = p.escola_id
                SET p.cidade_vinculo = e.cidade
                WHERE e.cidade IS NOT NULL AND TRIM(e.cidade) != \'\'
            ');
        }
    }

    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropColumn('cidade_vinculo');
        });

        Schema::table('professores', function (Blueprint $table) {
            $table->dropColumn('cidade_vinculo');
        });
    }
};
