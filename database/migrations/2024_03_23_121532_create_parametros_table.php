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
            
            $table->timestamps();
        });

        DB::table('parametros')->insert(
            ['ativ_dose' => 10, 'tempo_exames' => 60, 'vol_max_cq' => 6, 'tempo_exped' => 25, 'rend_tip_ciclotron' => 210, 'corrente_alvo' => 30, 'rend_sintese' => 55, 'tempo_sintese' => 30, 'vol_eos' => 30, 'hora_saida' => '09:00:00']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};
