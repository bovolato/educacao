<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplinas', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
        });

        Schema::table('disciplinas', function (Blueprint $table) {
            $table->unsignedBigInteger('municipio_id')->nullable()->change();
        });

        Schema::table('disciplinas', function (Blueprint $table) {
            $table->foreign('municipio_id')
                ->references('id')
                ->on('municipios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('disciplinas', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
        });

        Schema::table('disciplinas', function (Blueprint $table) {
            $table->unsignedBigInteger('municipio_id')->nullable(false)->change();
        });

        Schema::table('disciplinas', function (Blueprint $table) {
            $table->foreign('municipio_id')
                ->references('id')
                ->on('municipios')
                ->cascadeOnDelete();
        });
    }
};
