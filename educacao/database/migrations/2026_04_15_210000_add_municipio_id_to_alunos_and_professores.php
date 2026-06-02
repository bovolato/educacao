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
            $table->foreignId('municipio_id')->nullable()->after('user_id')
                ->constrained('municipios')->nullOnDelete();
        });

        Schema::table('professores', function (Blueprint $table) {
            $table->foreignId('municipio_id')->nullable()->after('escola_id')
                ->constrained('municipios')->nullOnDelete();
        });

        // Backfill: professor herda o município da escola vinculada.
        if (Schema::hasTable('escolas') && Schema::hasTable('professores')) {
            DB::statement('
                UPDATE professores AS p
                INNER JOIN escolas AS e ON e.id = p.escola_id
                SET p.municipio_id = e.municipio_id
                WHERE e.municipio_id IS NOT NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
            $table->dropColumn('municipio_id');
        });

        Schema::table('professores', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
            $table->dropColumn('municipio_id');
        });
    }
};
