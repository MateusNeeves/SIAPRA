<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('registros_lote', function (Blueprint $table) {
            $table->id();
            $table->boolean('completed')->default(false);
            $table->string('lote');
            $table->date('data_fabricacao');

            // PAGINA 3
                $table->string('lote_agua_enriquecida')->nullable();
                $table->unsignedBigInteger('id_usuario_lote_agua_enriquecida')->nullable();
                    $table->foreign('id_usuario_lote_agua_enriquecida')->references('id')->on('users');

                $table->decimal('pressao_ar_comprimido', 3, 1)->nullable();
                $table->decimal('pressao_H', 2, 1)->nullable();
                $table->decimal('pressao_He_refrigeracao', 2, 1)->nullable();
                $table->decimal('pressao_He_analitico', 2, 1)->nullable();
                $table->decimal('radiacao_ambiental_lab', 2, 1)->nullable();
                $table->unsignedBigInteger('id_usuario_verificacao_p3')->nullable();
                    $table->foreign('id_usuario_verificacao_p3')->references('id')->on('users');

                $table->time('hora_inicio_irradiacao_agua_enriquecida')->nullable();
                $table->time('hora_final_irradiacao_agua_enriquecida')->nullable();
                $table->decimal('ativ_teorica_F18')->nullable();
                $table->unsignedBigInteger('id_usuario_irradiacao_agua_enriquecida')->nullable();
                    $table->foreign('id_usuario_irradiacao_agua_enriquecida')->references('id')->on('users');

                $table->time('hora_inicio_transferir_F18_sintese')->nullable();
                $table->time('hora_final_transferir_F18_sintese')->nullable();
                $table->unsignedBigInteger('id_usuario_transferir_F18_sintese')->nullable();
                    $table->foreign('id_usuario_transferir_F18_sintese')->references('id')->on('users');

                $table->string('ocorrencias_p3', 290)->nullable();
                $table->time('ocorrencias_horario_p3')->nullable();
                $table->unsignedBigInteger('id_usuario_ocorrencias_p3')->nullable();
                    $table->foreign('id_usuario_ocorrencias_p3')->references('id')->on('users');
                
                $table->boolean('logbook_anexado')->nullable();
                $table->date('logbook_data')->nullable();
                $table->time('logbook_time')->nullable();
                $table->unsignedBigInteger('id_usuario_logbook')->nullable();
                    $table->foreign('id_usuario_logbook')->references('id')->on('users');
            
            // PAGINA 4
                
                $table->boolean('modulo_sintese')->nullable();

                $table->string('kryptofix222_lote')->nullable();
                $table->date('kryptofix222_data_validade')->nullable();
                $table->string('triflato_manose_lote')->nullable();
                $table->date('triflato_manose_data_validade')->nullable();
                $table->string('hidroxido_sodio_lote')->nullable();
                $table->date('hidroxido_sodio_data_validade')->nullable();
                $table->string('agua_injetaveis_lote')->nullable();
                $table->date('agua_injetaveis_data_validade')->nullable();
                $table->string('acetronitrila_anidra_lote')->nullable();
                $table->date('acetronitrila_anidra_data_validade')->nullable();
                $table->string('ifp_synthera_lote')->nullable();
                $table->date('ifp_synthera_data_validade')->nullable();
                
                $table->string('sep_pak_lote')->nullable();
                $table->date('sep_pak_data_validade')->nullable();
                $table->string('coluna_scx_lote')->nullable();
                $table->date('coluna_scx_data_validade')->nullable();
                $table->string('coluna_c18_lote')->nullable();
                $table->date('coluna_c18_data_validade')->nullable();
                $table->string('coluna_alumina_lote')->nullable();
                $table->date('coluna_alumina_data_validade')->nullable();
                $table->string('seringa_3ml_lote')->nullable();
                $table->date('seringa_3ml_data_validade')->nullable();
                $table->string('agulha_05x25_lote')->nullable();
                $table->date('agulha_05x25_data_validade')->nullable();
                $table->string('agua_injetavel_seringa_lote')->nullable();
                $table->date('agua_injetavel_seringa_data_validade')->nullable();
                $table->string('etanol_seringa_lote')->nullable();
                $table->date('etanol_seringa_data_validade')->nullable();
                $table->string('NaHCO3_seringa_lote')->nullable();
                $table->date('NaHCO3_seringa_data_validade')->nullable();

                $table->unsignedBigInteger('id_usuario_separado_registrado_p4')->nullable();
                    $table->foreign('id_usuario_separado_registrado_p4')->references('id')->on('users');
                $table->date('data_separado_registrado_p4')->nullable();
                $table->unsignedBigInteger('id_usuario_recebido_conferido_p4')->nullable();
                    $table->foreign('id_usuario_recebido_conferido_p4')->references('id')->on('users');
                $table->date('data_recebido_conferido_p4')->nullable();

            // PAGINA 5
                $table->time('hora_inicio_montagem_kit_synthera')->nullable();
                $table->time('hora_final_montagem_kit_synthera')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_montagem_kit_synthera')->nullable();
                    $table->foreign('id_usuario_execucao_montagem_kit_synthera')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_montagem_kit_synthera')->nullable();
                    $table->foreign('id_usuario_verificacao_montagem_kit_synthera')->references('id')->on('users');
                    
                $table->decimal('temperatura_lab_producao')->nullable();
                $table->decimal('umidade_lab_producao')->nullable();
                $table->unsignedBigInteger('id_usuario_verificacao_p5')->nullable();
                    $table->foreign('id_usuario_verificacao_p5')->references('id')->on('users');

                $table->boolean('limpeza_celula')->nullable();
                $table->boolean('verif_volume_H218O')->nullable();
                $table->boolean('verif_frasco_rejeitos')->nullable();
                $table->boolean('verif_bolsa_ar')->nullable();
                $table->boolean('abrir_valvula_ar_comprimido')->nullable();
                $table->boolean('abrir_valvula_nitrogenio')->nullable();
                $table->boolean('verif_pos_capilares')->nullable();
                $table->boolean('ligar_controle_synthera')->nullable();
                $table->boolean('ligar_notebook_synthera')->nullable();
                $table->boolean('iniciar_programa_mpb')->nullable();
                $table->boolean('retirar_ifp_usado')->nullable();
                $table->boolean('inserir_ifp_synthera')->nullable();
                $table->boolean('conectar_theodorico')->nullable();

            // PAGINA 6
                $table->boolean('iniciar_auto_teste')->nullable();
                $table->boolean('efetuar_diluicao_triflato_manose')->nullable();
                $table->boolean('remover_bloco_vermelho')->nullable();
                $table->boolean('fechar_portas_bbs')->nullable();
                $table->boolean('pressionar_start')->nullable();
                $table->unsignedBigInteger('id_usuario_verificacao_acoes')->nullable();
                    $table->foreign('id_usuario_verificacao_acoes')->references('id')->on('users');

                $table->decimal('ativ_chegada_18F')->nullable();
                $table->decimal('ativ_residual_18F')->nullable();
                $table->decimal('ativ_modulo_sintese')->nullable();
                $table->decimal('ativ_modulo_fracionamento')->nullable();
                $table->time('hora_inicio_sintese')->nullable();
                $table->time('hora_final_sintese')->nullable();
                $table->decimal('rendimento_sintese')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_p6')->nullable();
                    $table->foreign('id_usuario_execucao_p6')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_p6')->nullable();
                    $table->foreign('id_usuario_verificacao_p6')->references('id')->on('users');
                
                $table->string('ocorrencias_p6')->nullable();
                $table->time('ocorrencias_horario_p6')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_ocorrencias_p6')->nullable();
                    $table->foreign('id_usuario_execucao_ocorrencias_p6')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p6')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p6')->references('id')->on('users');    
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
