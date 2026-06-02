<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ra', 30)->nullable()->unique();
            $table->string('codigo_aluno', 30)->nullable()->unique();
            $table->string('nis', 20)->nullable();
            $table->string('sus', 20)->nullable();
            $table->boolean('necessidades_especiais')->default(false);
            $table->text('descricao_necessidades')->nullable();
            $table->text('observacoes_saude')->nullable();
            $table->boolean('usa_transporte')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
