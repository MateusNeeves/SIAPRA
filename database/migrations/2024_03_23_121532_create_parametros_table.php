<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
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
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};
