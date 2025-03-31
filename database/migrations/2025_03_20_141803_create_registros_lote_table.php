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

            $table->string('ocorrencias_p3', 290);
            $table->time('ocorrencias_horario_p3');
            $table->unsignedBigInteger('id_usuario_ocorrencias_p3');
                $table->foreign('id_usuario_ocorrencias_p3')->references('id')->on('users');
            
            $table->boolean('logbook_anexado');
            $table->date('logbook_data');
            $table->time('logbook_time');
            $table->unsignedBigInteger('id_usuario_logbook');
                $table->foreign('id_usuario_logbook')->references('id')->on('users');
        
        // PAGINA 4
            
            $table->boolean('modulo_sintese');

            $table->string('kryptofix222_lote');
            $table->date('kryptofix222_data_validade');
            $table->string('triflato_manose_lote');
            $table->date('triflato_manose_data_validade');
            $table->string('hidroxido_sodio_lote');
            $table->date('hidroxido_sodio_data_validade');
            $table->string('agua_injetaveis_lote');
            $table->date('agua_injetaveis_data_validade');
            $table->string('acetronitrila_anidra_lote');
            $table->date('acetronitrila_anidra_data_validade');
            $table->string('ifp_synthera_lote');
            $table->date('ifp_synthera_data_validade');
            
            $table->string('sep_pak_lote');
            $table->date('sep_pak_data_validade');
            $table->string('coluna_scx_lote');
            $table->date('coluna_scx_data_validade');
            $table->string('coluna_c18_lote');
            $table->date('coluna_c18_data_validade');
            $table->string('coluna_alumina_lote');
            $table->date('coluna_alumina_data_validade');
            $table->string('seringa_3ml_lote');
            $table->date('seringa_3ml_data_validade');
            $table->string('agulha_05x25_lote');
            $table->date('agulha_05x25_data_validade');
            $table->string('agua_injetavel_seringa_lote');
            $table->date('agua_injetavel_seringa_data_validade');
            $table->string('etanol_seringa_lote');
            $table->date('etanol_seringa_data_validade');
            $table->string('NaHCO3_seringa_lote');
            $table->date('NaHCO3_seringa_data_validade');

            $table->unsignedBigInteger('id_usuario_separado_registrado_p4');
                $table->foreign('id_usuario_separado_registrado_p4')->references('id')->on('users');
            $table->date('data_separado_registrado_p4');
            $table->unsignedBigInteger('id_usuario_recebido_conferido_p4');
                $table->foreign('id_usuario_recebido_conferido_p4')->references('id')->on('users');
            $table->date('data_recebido_conferido_p4');
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
