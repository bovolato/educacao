<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frequencias_bimestre_itens', function (Blueprint $table) {
            $table->unsignedSmallInteger('atrasos')->default(0)->after('faltas_justificadas');
        });
    }

    public function down(): void
    {
        Schema::table('frequencias_bimestre_itens', function (Blueprint $table) {
            $table->dropColumn('atrasos');
        });
    }
};

