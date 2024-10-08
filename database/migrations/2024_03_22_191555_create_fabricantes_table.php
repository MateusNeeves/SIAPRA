<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fabricantes', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('endereco');
            $table->string('pais');
            $table->string('nome_contato')->nullable();
            $table->string('telefone');
            $table->string('email')->nullable();
            $table->string('site')->nullable();
            $table->string('cnpj')->unique()->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabricantes');
    }
};
