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
            $table->string('lote')->unique();
                $table->foreign('lote')->references('lote')->on('planejamentos')->onDelete('cascade');
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

                $table->string('ocorrencias_p3', 260)->nullable();
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
                
                $table->string('ocorrencias_p6', 520)->nullable();
                $table->time('ocorrencias_horario_p6')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_ocorrencias_p6')->nullable();
                    $table->foreign('id_usuario_execucao_ocorrencias_p6')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p6')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p6')->references('id')->on('users');    

            // PAGINA 7
                $table->string('kit_fracionamento_1_lote')->nullable();
                $table->date('kit_fracionamento_1_data_validade')->nullable();
                $table->string('kit_fracionamento_2_lote')->nullable();
                $table->date('kit_fracionamento_2_data_validade')->nullable();
                $table->string('filtro_millex_gs_lote')->nullable();
                $table->date('filtro_millex_gs_data_validade')->nullable();
                $table->string('filtro_millex_gv_lote')->nullable();
                $table->date('filtro_millex_gv_data_validade')->nullable();
                $table->string('soro_fisiologico_lote')->nullable();
                $table->date('soro_fisiologico_data_validade')->nullable();
                $table->string('agulha_09x40_lote')->nullable();
                $table->date('agulha_09x40_data_validade')->nullable();
                $table->integer('frascos_15ml_qtd')->nullable();
                $table->string('frascos_15ml_lote')->nullable();
                $table->date('frascos_15ml_data_validade')->nullable();
                $table->string('frascos_bulk_lote')->nullable();
                $table->date('frascos_bulk_data_validade')->nullable();

                $table->unsignedBigInteger('id_usuario_separado_registrado_p7')->nullable();
                    $table->foreign('id_usuario_separado_registrado_p7')->references('id')->on('users');
                $table->date('data_separado_registrado_p7')->nullable();
                $table->unsignedBigInteger('id_usuario_recebido_conferido_p7')->nullable();
                    $table->foreign('id_usuario_recebido_conferido_p7')->references('id')->on('users');
                $table->date('data_recebido_conferido_p7')->nullable();

                $table->boolean('ligar_theodorico')->nullable();
                $table->boolean('colocar_castelo_chumbo_dws')->nullable();
                $table->boolean('pressionar_botao_park')->nullable();
                $table->boolean('pressionar_botao_pinch_open')->nullable();                
                $table->boolean('retirar_kit_usado')->nullable();                
                $table->boolean('realizar_limpeza_theodorico')->nullable();                
                $table->boolean('conectar_capilares_synthera_bulk')->nullable();                
                $table->boolean('conectar_kit_fracionamento_1')->nullable();                
                $table->boolean('fechar_bomba_peristaltica')->nullable();                
            
            // PAGINA 8
                $table->boolean('pressionar_botao_pinch_close')->nullable();
                $table->boolean('conectar_kit_fracionamento_2')->nullable();
                $table->boolean('prender_capilares_parede_theodorico')->nullable();
                $table->boolean('conectar_filtros_millex_gs')->nullable();
                $table->boolean('verificar_linhas_conectadas')->nullable();
                $table->boolean('verificar_conexoes_capilares')->nullable();
                $table->boolean('verificar_agulha_succao')->nullable();
                $table->boolean('fechar_porta')->nullable();
                $table->boolean('programar_fracionamento_software')->nullable();
                $table->boolean('imprimir_etiqueta_frascos')->nullable();
                $table->boolean('alimentar_antecamara_frascos')->nullable();
                $table->boolean('marcar_posicao_frascos')->nullable();
                $table->boolean('pressionar_botao_from_synt')->nullable();
                $table->boolean('pressionar_botao_bulk_dilution')->nullable();
                $table->boolean('pressionar_botao_start')->nullable();
                $table->unsignedBigInteger('id_usuario_verificado_p8')->nullable();
                    $table->foreign('id_usuario_verificado_p8')->references('id')->on('users');

                $table->decimal('atividade_fdg_18f')->nullable();
                $table->decimal('volume_soro_fisiologico')->nullable();
                $table->boolean('imprimir_anexar_relatorio_producao')->nullable();
                $table->time('hora_inicio_p8')->nullable();
                $table->time('hora_final_p8')->nullable();
                $table->unsignedBigInteger('id_usuario_fracionamento_executado')->nullable();
                    $table->foreign('id_usuario_fracionamento_executado')->references('id')->on('users');
            
            // PAGINA 9
                $table->string('ocorrencias_p9', 820)->nullable();
                $table->time('ocorrencias_horario_p9')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_ocorrencias_p9')->nullable();
                    $table->foreign('id_usuario_execucao_ocorrencias_p9')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p9')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p9')->references('id')->on('users'); 

            // PAGINA 10
                $table->integer('embalagem_balde_qtd')->nullable();
                $table->integer('embalagem_balde_separado')->nullable();
                $table->boolean('embalagem_balde_conferido')->nullable();
                $table->integer('embalagem_case_qtd')->nullable();
                $table->integer('embalagem_case_separado')->nullable();
                $table->boolean('embalagem_case_conferido')->nullable();
                $table->integer('etiquetas_it_qtd')->nullable();
                $table->integer('etiquetas_it_separado')->nullable();
                $table->boolean('etiquetas_it_conferido')->nullable();
                $table->integer('bulas_fdg_qtd')->nullable();
                $table->integer('bulas_fdg_separado')->nullable();
                $table->boolean('bulas_fdg_conferido')->nullable();
                $table->unsignedBigInteger('id_usuario_separado_embalagem_p10')->nullable();
                    $table->foreign('id_usuario_separado_embalagem_p10')->references('id')->on('users');
                $table->time('horario_separado_embalagem_p10')->nullable();
                $table->unsignedBigInteger('id_usuario_conferido_embalagem_p10')->nullable();
                    $table->foreign('id_usuario_conferido_embalagem_p10')->references('id')->on('users');
                $table->time('horario_conferido_embalagem_p10')->nullable();

                $table->integer('decl_exped_qtd')->nullable();
                $table->integer('decl_exped_separado')->nullable();
                $table->boolean('decl_exped_conferido')->nullable();
                $table->integer('ficha_emerg_qtd')->nullable();
                $table->integer('ficha_emerg_separado')->nullable();
                $table->boolean('ficha_emerg_conferido')->nullable();
                $table->integer('nota_fiscal_qtd')->nullable();
                $table->integer('nota_fiscal_separado')->nullable();
                $table->boolean('nota_fiscal_conferido')->nullable();
                $table->integer('termo_doacao_qtd')->nullable();
                $table->integer('termo_doacao_separado')->nullable();
                $table->boolean('termo_doacao_conferido')->nullable();
                $table->integer('ident_veiculo_qtd')->nullable();
                $table->integer('ident_veiculo_separado')->nullable();
                $table->boolean('ident_veiculo_conferido')->nullable();
                $table->integer('form_tam_qtd')->nullable();
                $table->integer('form_tam_separado')->nullable();
                $table->boolean('form_tam_conferido')->nullable();
                $table->integer('form_iata_qtd')->nullable();
                $table->integer('form_iata_separado')->nullable();
                $table->boolean('form_iata_conferido')->nullable();
                $table->unsignedBigInteger('id_usuario_separado_expedicao_p10')->nullable();
                    $table->foreign('id_usuario_separado_expedicao_p10')->references('id')->on('users');
                $table->time('horario_separado_expedicao_p10')->nullable();
                $table->unsignedBigInteger('id_usuario_conferido_expedicao_p10')->nullable();
                    $table->foreign('id_usuario_conferido_expedicao_p10')->references('id')->on('users');
                $table->time('horario_conferido_expedicao_p10')->nullable();

                $table->time('horario_final_emb_exped')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_p10')->nullable();
                    $table->foreign('id_usuario_execucao_p10')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_p10')->nullable();
                    $table->foreign('id_usuario_verificacao_p10')->references('id')->on('users');

                $table->string('ocorrencias_p10', 200)->nullable();
                $table->time('ocorrencias_horario_p10')->nullable();
                $table->unsignedBigInteger('id_usuario_execucao_ocorrencias_p10')->nullable();
                    $table->foreign('id_usuario_execucao_ocorrencias_p10')->references('id')->on('users');
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p10')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p10')->references('id')->on('users'); 
            
            //PAGINA 11
                $table->string('aspecto_resultado')->nullable();
                $table->date('aspecto_data')->nullable();
                $table->unsignedBigInteger('id_usuario_aspecto')->nullable();
                    $table->foreign('id_usuario_aspecto')->references('id')->on('users');
                $table->decimal('ph_1_resultado', 3, 1)->nullable();
                $table->date('ph_1_data')->nullable();
                $table->unsignedBigInteger('id_usuario_ph_1')->nullable();
                    $table->foreign('id_usuario_ph_1')->references('id')->on('users');
                $table->decimal('ph_2_resultado', 3, 1)->nullable();
                $table->date('ph_2_data')->nullable();
                $table->unsignedBigInteger('id_usuario_ph_2')->nullable();
                    $table->foreign('id_usuario_ph_2')->references('id')->on('users');
                $table->string('pureza_radionuclidica_1_resultado')->nullable();
                $table->date('pureza_radionuclidica_1_data')->nullable();
                $table->unsignedBigInteger('id_usuario_pureza_radionuclidica_1')->nullable();
                    $table->foreign('id_usuario_pureza_radionuclidica_1')->references('id')->on('users');
                $table->string('pureza_radionuclidica_2_resultado')->nullable();
                $table->date('pureza_radionuclidica_2_data')->nullable();
                $table->unsignedBigInteger('id_usuario_pureza_radionuclidica_2')->nullable();
                    $table->foreign('id_usuario_pureza_radionuclidica_2')->references('id')->on('users');
                $table->string('meia_vida_resultado')->nullable();
                $table->date('meia_vida_data')->nullable();
                $table->unsignedBigInteger('id_usuario_meia_vida')->nullable();
                    $table->foreign('id_usuario_meia_vida')->references('id')->on('users');
                $table->string('solventes_resultado')->nullable();
                $table->date('solventes_data')->nullable();
                $table->unsignedBigInteger('id_usuario_solventes')->nullable();
                    $table->foreign('id_usuario_solventes')->references('id')->on('users');

                $table->string('ocorrencias_p11', 220)->nullable();
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p11')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p11')->references('id')->on('users');
        
            //PAGINA 12 
                $table->boolean('pureza_radioquimica_a_codigo')->nullable();
                $table->string('pureza_radioquimica_a_resultado')->nullable();
                $table->date('pureza_radioquimica_a_data')->nullable();
                $table->unsignedBigInteger('id_usuario_pureza_radioquimica_a')->nullable();
                    $table->foreign('id_usuario_pureza_radioquimica_a')->references('id')->on('users');
                $table->string('pureza_radioquimica_b_resultado')->nullable();
                $table->date('pureza_radioquimica_b_data')->nullable();
                $table->unsignedBigInteger('id_usuario_pureza_radioquimica_b')->nullable();
                    $table->foreign('id_usuario_pureza_radioquimica_b')->references('id')->on('users');
                $table->string('pureza_quimica_resultado')->nullable();
                $table->date('pureza_quimica_data')->nullable();
                $table->unsignedBigInteger('id_usuario_pureza_quimica')->nullable();
                    $table->foreign('id_usuario_pureza_quimica')->references('id')->on('users');
            
                $table->string('ocorrencias_p12', 220)->nullable();
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p12')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p12')->references('id')->on('users');

                $table->boolean('aprovacao_fisico_quimico')->nullable();
                $table->date('data_aprovacao_fisico_quimico')->nullable();
                $table->unsignedBigInteger('id_usuario_aprovacao_fisico_quimico')->nullable();
                    $table->foreign('id_usuario_aprovacao_fisico_quimico')->references('id')->on('users');
            
            //PAGINA 13
                $table->boolean('endotoxinas_codigo')->nullable();
                $table->decimal('endotoxinas_1_resultado')->nullable();
                $table->decimal('endotoxinas_2_resultado')->nullable();
                $table->decimal('endotoxinas_3_resultado')->nullable();
                $table->decimal('endotoxinas_4_resultado')->nullable();
                $table->date('endotoxinas_data')->nullable();
                $table->unsignedBigInteger('id_usuario_endotoxinas')->nullable();
                    $table->foreign('id_usuario_endotoxinas')->references('id')->on('users');
                $table->string('codigo_calibracao_pts')->nullable();
                $table->string('lote_cartucho_pts')->nullable();

                $table->string('membrana_equipamento')->nullable();
                $table->string('membrana_lote')->nullable();
                $table->date('membrana_validade')->nullable();
                $table->unsignedBigInteger('id_usuario_membrana')->nullable();
                    $table->foreign('id_usuario_membrana')->references('id')->on('users');

                $table->string('pressao_teste_bolha_fornecida')->nullable();
                $table->string('pressao_teste_bolha_obtida')->nullable();
                $table->unsignedBigInteger('id_usuario_pressao_teste_bolha')->nullable();
                    $table->foreign('id_usuario_pressao_teste_bolha')->references('id')->on('users');

                $table->string('ocorrencias_p13', 220)->nullable();
                $table->unsignedBigInteger('id_usuario_verificacao_ocorrencias_p13')->nullable();
                    $table->foreign('id_usuario_verificacao_ocorrencias_p13')->references('id')->on('users');

                $table->boolean('aprovacao_microbiologico')->nullable();
                $table->date('data_aprovacao_microbiologico')->nullable();
                $table->unsignedBigInteger('id_usuario_aprovacao_microbiologico')->nullable();
                    $table->foreign('id_usuario_aprovacao_microbiologico')->references('id')->on('users');
                
            // PAGINA 14
                $table->date('esterilidade_data_inicio_analise')->nullable();
                $table->unsignedBigInteger('id_usuario_esterilidade')->nullable();
                    $table->foreign('id_usuario_esterilidade')->references('id')->on('users');

                $table->integer('esterilidade_codigo')->nullable();
                $table->string('esterilidade_1_resultado')->nullable();
                $table->date('esterilidade_1_data')->nullable();
                $table->unsignedBigInteger('id_usuario_esterilidade_1')->nullable();
                    $table->foreign('id_usuario_esterilidade_1')->references('id')->on('users');
                $table->string('esterilidade_2_resultado')->nullable();
                $table->date('esterilidade_2_data')->nullable();
                $table->unsignedBigInteger('id_usuario_esterilidade_2')->nullable();
                    $table->foreign('id_usuario_esterilidade_2')->references('id')->on('users');
                $table->string('esterilidade_3_resultado')->nullable();
                $table->date('esterilidade_3_data')->nullable();
                $table->unsignedBigInteger('id_usuario_esterilidade_3')->nullable();
                    $table->foreign('id_usuario_esterilidade_3')->references('id')->on('users');

                $table->string('ocorrencias_p14', 260)->nullable();
              
                $table->boolean('aprovacao_esterilidade')->nullable();
                $table->date('data_aprovacao_esterilidade')->nullable();
                $table->unsignedBigInteger('id_usuario_aprovacao_esterilidade')->nullable();
                    $table->foreign('id_usuario_aprovacao_esterilidade')->references('id')->on('users');

            // PAGINA 15
                $table->unsignedBigInteger('id_usuario_supervisor_controle_qualidade')->nullable();
                    $table->foreign('id_usuario_supervisor_controle_qualidade')->references('id')->on('users');
                $table->boolean('atendimento_criterios')->nullable();
                $table->boolean('aprovacao_lote')->nullable();
                $table->unsignedBigInteger('id_usuario_resposavel_garantia_qualidade')->nullable();
                    $table->foreign('id_usuario_resposavel_garantia_qualidade')->references('id')->on('users');
                $table->time('hora_emissao_laudo')->nullable();
            
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
