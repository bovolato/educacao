<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pessoa_id')->nullable()->after('id')->constrained('pessoas')->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->after('pessoa_id')->constrained('municipios')->nullOnDelete();
            $table->foreignId('escola_id')->nullable()->after('municipio_id')->constrained('escolas')->nullOnDelete();
            $table->string('username', 60)->nullable()->unique()->after('email');
            $table->timestamp('ultimo_login_em')->nullable()->after('remember_token');
            $table->string('ultimo_ip', 45)->nullable()->after('ultimo_login_em');
            $table->boolean('ativo')->default(true)->after('ultimo_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pessoa_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['escola_id']);
            $table->dropColumn(['pessoa_id', 'municipio_id', 'escola_id', 'username', 'ultimo_login_em', 'ultimo_ip', 'ativo']);
        });
    }
};
