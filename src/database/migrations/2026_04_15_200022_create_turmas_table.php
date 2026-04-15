<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->foreignId('ano_letivo_id')->constrained('anos_letivos')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('series')->cascadeOnDelete();
            $table->foreignId('turno_id')->constrained('turnos')->cascadeOnDelete();
            $table->foreignId('sala_id')->nullable()->constrained('salas')->nullOnDelete();
            $table->string('nome');
            $table->string('codigo', 20)->nullable();
            $table->integer('capacidade')->default(30);
            $table->enum('status', ['ativa', 'encerrada', 'cancelada'])->default('ativa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
