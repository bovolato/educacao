<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // A ideia é não “sumir” com dados legados.
        // Backfill padrão: tudo existente entra como 1B.
        Schema::table('aulas', function (Blueprint $table) {
            $table->string('periodo', 3)->default('1B')->index();
        });
        DB::table('aulas')->whereNull('periodo')->update(['periodo' => '1B']);

        Schema::table('conteudos_aula', function (Blueprint $table) {
            $table->string('periodo', 3)->default('1B')->index();
        });
        DB::table('conteudos_aula')->whereNull('periodo')->update(['periodo' => '1B']);

        Schema::table('tarefas', function (Blueprint $table) {
            $table->string('periodo', 3)->default('1B')->index();
        });
        DB::table('tarefas')->whereNull('periodo')->update(['periodo' => '1B']);

        Schema::table('materiais_didaticos', function (Blueprint $table) {
            $table->string('periodo', 3)->default('1B')->index();
        });
        DB::table('materiais_didaticos')->whereNull('periodo')->update(['periodo' => '1B']);

        Schema::table('planos_ensino', function (Blueprint $table) {
            $table->string('periodo', 3)->default('1B')->index();
        });
        DB::table('planos_ensino')->whereNull('periodo')->update(['periodo' => '1B']);

        Schema::table('planos_aula', function (Blueprint $table) {
            $table->string('periodo', 3)->default('1B')->index();
        });
        DB::table('planos_aula')->whereNull('periodo')->update(['periodo' => '1B']);
    }

    public function down(): void
    {
        Schema::table('planos_aula', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
        Schema::table('planos_ensino', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
        Schema::table('materiais_didaticos', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
        Schema::table('tarefas', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
        Schema::table('conteudos_aula', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
        Schema::table('aulas', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
    }
};

