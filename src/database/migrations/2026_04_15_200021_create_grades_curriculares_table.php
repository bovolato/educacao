<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades_curriculares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
            $table->foreignId('etapa_ensino_id')->constrained('etapas_ensino')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('series')->cascadeOnDelete();
            $table->foreignId('ano_letivo_id')->constrained('anos_letivos')->cascadeOnDelete();
            $table->string('nome');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('grade_disciplinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_curricular_id')->constrained('grades_curriculares')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->integer('carga_horaria')->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('obrigatoria')->default(true);
            $table->timestamps();

            $table->unique(['grade_curricular_id', 'disciplina_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_disciplinas');
        Schema::dropIfExists('grades_curriculares');
    }
};
