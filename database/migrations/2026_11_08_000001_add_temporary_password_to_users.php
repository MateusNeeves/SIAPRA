<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('temp_password')->nullable();
            $table->timestamp('temp_password_expires_at')->nullable();
        });
        foreach (['Recuperação emitida', 'Recuperação: falha de envio', 'Autenticação temporária', 'Recuperação concluída', 'Recuperação cancelada'] as $action) {
            if (! DB::table('acoes')->where('descricao', $action)->exists()) {
                DB::table('acoes')->insert(['descricao' => $action]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['temp_password', 'temp_password_expires_at']));
        // Preserve identifiers referenced by audit records.
    }
};
