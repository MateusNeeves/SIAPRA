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
        Schema::create('registros_lote', function (Blueprint $table) {
            $table->id();
            $table->boolean('completed')->default(false);
            $table->string('lote');
            $table->date('data_fabricacao');

        // PAGINA 3
            $table->string('lote_agua_enriquecida');
            $table->unsignedBigInteger('id_usuario_lote_agua_enriquecida');
                $table->foreign('id_usuario_lote_agua_enriquecida')->references('id')->on('users');

            $table->decimal('pressao_ar_comprimido', 3, 1);
            $table->decimal('pressao_H', 2, 1);
            $table->decimal('pressao_He_refrigeracao', 2, 1);
            $table->decimal('pressao_He_analitico', 2, 1);
            $table->decimal('radiacao_ambiental_lab', 2, 1);
            $table->unsignedBigInteger('id_usuario_verificacao_p3');
                $table->foreign('id_usuario_verificacao_p3')->references('id')->on('users');

            $table->time('hora_inicio_irradiacao_agua_enriquecida');
            $table->time('hora_final_irradiacao_agua_enriquecida');
            $table->decimal('ativ_teorica_F18');
            $table->unsignedBigInteger('id_usuario_irradiacao_agua_enriquecida');
                $table->foreign('id_usuario_irradiacao_agua_enriquecida')->references('id')->on('users');

            $table->time('hora_inicio_transferir_F18_sintese');
            $table->time('hora_final_transferir_F18_sintese');
            $table->unsignedBigInteger('id_usuario_transferir_F18_sintese');
                $table->foreign('id_usuario_transferir_F18_sintese')->references('id')->on('users');

            $table->string('ocorrencias_p3');
            $table->time('ocorrencias_horario_p3');
            $table->unsignedBigInteger('id_usuario_ocorrencias_p3');
                $table->foreign('id_usuario_ocorrencias_p3')->references('id')->on('users');
            
            $table->boolean('logbook_anexado');
            $table->date('logbook_data');
            $table->time('logbook_time');
            $table->unsignedBigInteger('id_usuario_logbook');
                $table->foreign('id_usuario_logbook')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_lote');
    }
};
