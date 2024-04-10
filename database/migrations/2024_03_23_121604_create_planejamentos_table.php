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
        Schema::create('planejamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->date('data_producao');

            $table->integer('fator_seguranca');

            // Parâmetros
            $table->integer('ativ_dose');
            $table->integer('tempo_exames');
            $table->integer('vol_max_cq');
            $table->integer('tempo_exped');
            $table->integer('rend_tip_ciclotron');
            $table->integer('corrente_alvo');
            $table->integer('rend_sintese');
            $table->integer('tempo_sintese');
            $table->integer('vol_eos');
            $table->time('hora_saida');

            // Calculados

            $table->float('duracao_ciclotron');
            $table->float('ativ_eob');
            $table->float('ativ_eos');
            $table->float('ativ_esp');

            $table->foreign('id_usuario')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planejamentos');
    }
};
