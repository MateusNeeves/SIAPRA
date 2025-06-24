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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('descricao');
            $table->unsignedBigInteger('id_tipo');
            $table->decimal('qtd_aceitavel', 10, 3);
            $table->decimal('qtd_minima', 10, 3);
            $table->string('quarentena');
            $table->unsignedBigInteger('id_unidade_medida');

            $table->foreign('id_tipo')->references('id')->on('tipos_produtos');
            $table->foreign('id_unidade_medida')->references('id')->on('unidade_medida');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
