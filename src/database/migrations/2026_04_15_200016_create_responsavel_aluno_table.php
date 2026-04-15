<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsavel_aluno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responsavel_id')->constrained('responsaveis')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->string('grau_parentesco')->nullable();
            $table->boolean('responsavel_principal')->default(false);
            $table->boolean('retira_aluno')->default(false);
            $table->boolean('recebe_boletim')->default(true);
            $table->timestamps();

            $table->unique(['responsavel_id', 'aluno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsavel_aluno');
    }
};
