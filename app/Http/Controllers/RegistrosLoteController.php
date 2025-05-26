<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\User;
use setasign\Fpdi\Fpdi;
use App\Models\Planejamento;
use Illuminate\Http\Request;
use App\Models\Registro_Lote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrosLoteController extends Controller
{
    public function index(){
        $lotes = Planejamento::select('lote', 'data_producao')->get();
        return view('registros_lote/visualizar', ['lotes' => $lotes]);
        // $datas = Registro_Lote::select('data_fabricacao')->distinct()->pluck('data_fabricacao');
        // return view('registros_lote/visualizar', ['datas' => $datas]);
    }

    public function register(Request $request){
        $registro = Registro_Lote::where('lote', $request->loteSelect)->first();
        if (!$registro) {
            $registro = [
                'lote' => $request->loteSelect,
                'data_fabricacao' => $request->data_fabricacao, 
            ];
        }

        // return response()->json($registro);

        $usuarios = User::all();
        return view('registros_lote/cadastrar', ['registro' => $registro, 'usuarios' => $usuarios]);
    }

    public function store(Request $request){
        $dataParts = explode('-', $request->lote)[0]; // Gets "YYYY_MM_DD"
        $formattedDate = str_replace('_', '-', $dataParts); // Convert to "YYYY-MM-DD"
        
        $modifiedFields = [
            'data_fabricacao' => $formattedDate,
            'completed' => false,
        ];
        
        if ($request->action == 'partialIrradiacao') {
            $modifiedFields['lote_agua_enriquecida'] = $request->lote_agua_enriquecida;
            $modifiedFields['id_usuario_lote_agua_enriquecida'] = $request->id_usuario_lote_agua_enriquecida;
            
            $modifiedFields['pressao_ar_comprimido'] = $request->pressao_ar_comprimido;
            $modifiedFields['pressao_H'] = $request->pressao_H;
            $modifiedFields['pressao_He_refrigeracao'] = $request->pressao_He_refrigeracao;
            $modifiedFields['pressao_He_analitico'] = $request->pressao_He_analitico;
            $modifiedFields['radiacao_ambiental_lab'] = $request->radiacao_ambiental_lab;
            $modifiedFields['id_usuario_verificacao_p3'] = $request->id_usuario_verificacao_p3;

            $modifiedFields['hora_inicio_irradiacao_agua_enriquecida'] = $request->hora_inicio_irradiacao_agua_enriquecida;
            $modifiedFields['hora_final_irradiacao_agua_enriquecida'] = $request->hora_final_irradiacao_agua_enriquecida;
            $modifiedFields['ativ_teorica_F18'] = $request->ativ_teorica_F18;
            $modifiedFields['id_usuario_irradiacao_agua_enriquecida'] = $request->id_usuario_irradiacao_agua_enriquecida;

            $modifiedFields['hora_inicio_transferir_F18_sintese'] = $request->hora_inicio_transferir_F18_sintese;
            $modifiedFields['hora_final_transferir_F18_sintese'] = $request->hora_final_transferir_F18_sintese;
            $modifiedFields['id_usuario_transferir_F18_sintese'] = $request->id_usuario_transferir_F18_sintese;
            
            $modifiedFields['ocorrencias_p3'] = $request->ocorrencias_p3;
            $modifiedFields['ocorrencias_horario_p3'] = $request->ocorrencias_horario_p3;
            $modifiedFields['id_usuario_ocorrencias_p3'] = $request->id_usuario_ocorrencias_p3;

            $modifiedFields['logbook_anexado'] = $request->logbook_anexado;
            $modifiedFields['logbook_data'] = $request->logbook_data;
            $modifiedFields['logbook_time'] = $request->logbook_time;
            $modifiedFields['id_usuario_logbook'] = $request->id_usuario_logbook;
        }

        if ($request->action == 'partialSintese') {
            $modifiedFields['modulo_sintese'] = $request->modulo_sintese;

            $modifiedFields['kryptofix222_lote'] = $request->kryptofix222_lote;
            $modifiedFields['kryptofix222_data_validade'] = $request->kryptofix222_data_validade;
            $modifiedFields['triflato_manose_lote'] = $request->triflato_manose_lote;
            $modifiedFields['triflato_manose_data_validade'] = $request->triflato_manose_data_validade;
            $modifiedFields['hidroxido_sodio_lote'] = $request->hidroxido_sodio_lote;
            $modifiedFields['hidroxido_sodio_data_validade'] = $request->hidroxido_sodio_data_validade;
            $modifiedFields['agua_injetaveis_lote'] = $request->agua_injetaveis_lote;
            $modifiedFields['agua_injetaveis_data_validade'] = $request->agua_injetaveis_data_validade;
            $modifiedFields['acetronitrila_anidra_lote'] = $request->acetronitrila_anidra_lote;
            $modifiedFields['acetronitrila_anidra_data_validade'] = $request->acetronitrila_anidra_data_validade;
            $modifiedFields['ifp_synthera_lote'] = $request->ifp_synthera_lote;
            $modifiedFields['ifp_synthera_data_validade'] = $request->ifp_synthera_data_validade;

            $modifiedFields['sep_pak_lote'] = $request->sep_pak_lote;
            $modifiedFields['sep_pak_data_validade'] = $request->sep_pak_data_validade;
            $modifiedFields['coluna_scx_lote'] = $request->coluna_scx_lote;
            $modifiedFields['coluna_scx_data_validade'] = $request->coluna_scx_data_validade;
            $modifiedFields['coluna_c18_lote'] = $request->coluna_c18_lote;
            $modifiedFields['coluna_c18_data_validade'] = $request->coluna_c18_data_validade;
            $modifiedFields['coluna_alumina_lote'] = $request->coluna_alumina_lote;
            $modifiedFields['coluna_alumina_data_validade'] = $request->coluna_alumina_data_validade;
            $modifiedFields['seringa_3ml_lote'] = $request->seringa_3ml_lote;
            $modifiedFields['seringa_3ml_data_validade'] = $request->seringa_3ml_data_validade;
            $modifiedFields['agulha_05x25_lote'] = $request->agulha_05x25_lote;
            $modifiedFields['agulha_05x25_data_validade'] = $request->agulha_05x25_data_validade;
            $modifiedFields['agua_injetavel_seringa_lote'] = $request->agua_injetavel_seringa_lote;
            $modifiedFields['agua_injetavel_seringa_data_validade'] = $request->agua_injetavel_seringa_data_validade;
            $modifiedFields['etanol_seringa_lote'] = $request->etanol_seringa_lote;
            $modifiedFields['etanol_seringa_data_validade'] = $request->etanol_seringa_data_validade;
            $modifiedFields['NaHCO3_seringa_lote'] = $request->NaHCO3_seringa_lote;
            $modifiedFields['NaHCO3_seringa_data_validade'] = $request->NaHCO3_seringa_data_validade;

            $modifiedFields['id_usuario_separado_registrado_p4'] = $request->id_usuario_separado_registrado_p4;
            $modifiedFields['data_separado_registrado_p4'] = $request->data_separado_registrado_p4;

            $modifiedFields['id_usuario_recebido_conferido_p4'] = $request->id_usuario_recebido_conferido_p4;
            $modifiedFields['data_recebido_conferido_p4'] = $request->data_recebido_conferido_p4;
            
            $modifiedFields['hora_inicio_montagem_kit_synthera'] = $request->hora_inicio_montagem_kit_synthera;
            $modifiedFields['hora_final_montagem_kit_synthera'] = $request->hora_final_montagem_kit_synthera;
            $modifiedFields['id_usuario_execucao_montagem_kit_synthera'] = $request->id_usuario_execucao_montagem_kit_synthera;
            $modifiedFields['id_usuario_verificacao_montagem_kit_synthera'] = $request->id_usuario_verificacao_montagem_kit_synthera;
            
            $modifiedFields['temperatura_lab_producao'] = $request->temperatura_lab_producao;
            $modifiedFields['umidade_lab_producao'] = $request->umidade_lab_producao;
            $modifiedFields['id_usuario_verificacao_p5'] = $request->id_usuario_verificacao_p5;

            $modifiedFields['limpeza_celula'] = $request->has('limpeza_celula') ? true : false;
            $modifiedFields['verif_volume_H218O'] = $request->has('verif_volume_H218O') ? true : false;
            $modifiedFields['verif_frasco_rejeitos'] = $request->has('verif_frasco_rejeitos') ? true : false;
            $modifiedFields['verif_bolsa_ar'] = $request->has('verif_bolsa_ar') ? true : false;
            $modifiedFields['abrir_valvula_ar_comprimido'] = $request->has('abrir_valvula_ar_comprimido') ? true : false;
            $modifiedFields['abrir_valvula_nitrogenio'] = $request->has('abrir_valvula_nitrogenio') ? true : false;
            $modifiedFields['verif_pos_capilares'] = $request->has('verif_pos_capilares') ? true : false;
            $modifiedFields['ligar_controle_synthera'] = $request->has('ligar_controle_synthera') ? true : false;
            $modifiedFields['ligar_notebook_synthera'] = $request->has('ligar_notebook_synthera') ? true : false;
            $modifiedFields['iniciar_programa_mpb'] = $request->has('iniciar_programa_mpb') ? true : false;
            $modifiedFields['retirar_ifp_usado'] = $request->has('retirar_ifp_usado') ? true : false;
            $modifiedFields['inserir_ifp_synthera'] = $request->has('inserir_ifp_synthera') ? true : false;
            $modifiedFields['conectar_theodorico'] = $request->has('conectar_theodorico') ? true : false;

            $modifiedFields['iniciar_auto_teste'] = $request->has('iniciar_auto_teste') ? true : false;
            $modifiedFields['efetuar_diluicao_triflato_manose'] = $request->has('efetuar_diluicao_triflato_manose') ? true : false;
            $modifiedFields['remover_bloco_vermelho'] = $request->has('remover_bloco_vermelho') ? true : false;
            $modifiedFields['fechar_portas_bbs'] = $request->has('fechar_portas_bbs') ? true : false;
            $modifiedFields['pressionar_start'] = $request->has('pressionar_start') ? true : false;
            $modifiedFields['id_usuario_verificacao_acoes'] = $request->id_usuario_verificacao_acoes;

            $modifiedFields['ativ_chegada_18F'] = $request->ativ_chegada_18F;
            $modifiedFields['ativ_residual_18F'] = $request->ativ_residual_18F;
            $modifiedFields['ativ_modulo_sintese'] = $request->ativ_modulo_sintese;
            $modifiedFields['ativ_modulo_fracionamento'] = $request->ativ_modulo_fracionamento;
            $modifiedFields['hora_inicio_sintese'] = $request->hora_inicio_sintese;
            $modifiedFields['hora_final_sintese'] = $request->hora_final_sintese;
            $modifiedFields['rendimento_sintese'] = $request->rendimento_sintese;
            $modifiedFields['id_usuario_execucao_p6'] = $request->id_usuario_execucao_p6;
            $modifiedFields['id_usuario_verificacao_p6'] = $request->id_usuario_verificacao_p6;

            $modifiedFields['ocorrencias_p6'] = $request->ocorrencias_p6;
            $modifiedFields['ocorrencias_horario_p6'] = $request->ocorrencias_horario_p6;
            $modifiedFields['id_usuario_execucao_ocorrencias_p6'] = $request->id_usuario_execucao_ocorrencias_p6;
            $modifiedFields['id_usuario_verificacao_ocorrencias_p6'] = $request->id_usuario_verificacao_ocorrencias_p6;
        }

        if ($request->action == 'partialFracionamento') {
            $modifiedFields['kit_fracionamento_1_lote'] = $request->kit_fracionamento_1_lote;
            $modifiedFields['kit_fracionamento_1_data_validade'] = $request->kit_fracionamento_1_data_validade;
            $modifiedFields['kit_fracionamento_2_lote'] = $request->kit_fracionamento_2_lote;
            $modifiedFields['kit_fracionamento_2_data_validade'] = $request->kit_fracionamento_2_data_validade;
            $modifiedFields['filtro_millex_gs_lote'] = $request->filtro_millex_gs_lote;
            $modifiedFields['filtro_millex_gs_data_validade'] = $request->filtro_millex_gs_data_validade;
            $modifiedFields['filtro_millex_gv_lote'] = $request->filtro_millex_gv_lote;
            $modifiedFields['filtro_millex_gv_data_validade'] = $request->filtro_millex_gv_data_validade;
            $modifiedFields['soro_fisiologico_lote'] = $request->soro_fisiologico_lote;
            $modifiedFields['soro_fisiologico_data_validade'] = $request->soro_fisiologico_data_validade;
            $modifiedFields['agulha_09x40_lote'] = $request->agulha_09x40_lote;
            $modifiedFields['agulha_09x40_data_validade'] = $request->agulha_09x40_data_validade;
            $modifiedFields['frascos_15ml_lote'] = $request->frascos_15ml_lote;
            $modifiedFields['frascos_15ml_qtd'] = $request->frascos_15ml_qtd;
            $modifiedFields['frascos_15ml_data_validade'] = $request->frascos_15ml_data_validade;
            $modifiedFields['frascos_bulk_lote'] = $request->frascos_bulk_lote;
            $modifiedFields['frascos_bulk_data_validade'] = $request->frascos_bulk_data_validade;
            $modifiedFields['id_usuario_separado_registrado_p7'] = $request->id_usuario_separado_registrado_p7;
            $modifiedFields['data_separado_registrado_p7'] = $request->data_separado_registrado_p7;
            $modifiedFields['id_usuario_recebido_conferido_p7'] = $request->id_usuario_recebido_conferido_p7;
            $modifiedFields['data_recebido_conferido_p7'] = $request->data_recebido_conferido_p7;

            $modifiedFields['ligar_theodorico'] = $request->has('ligar_theodorico') ? true : false;
            $modifiedFields['colocar_castelo_chumbo_dws'] = $request->has('colocar_castelo_chumbo_dws') ? true : false;
            $modifiedFields['pressionar_botao_park'] = $request->has('pressionar_botao_park') ? true : false;
            $modifiedFields['pressionar_botao_pinch_open'] = $request->has('pressionar_botao_pinch_open') ? true : false;
            $modifiedFields['retirar_kit_usado'] = $request->has('retirar_kit_usado') ? true : false;
            $modifiedFields['realizar_limpeza_theodorico'] = $request->has('realizar_limpeza_theodorico') ? true : false;
            $modifiedFields['conectar_capilares_synthera_bulk'] = $request->has('conectar_capilares_synthera_bulk') ? true : false;
            $modifiedFields['conectar_kit_fracionamento_1'] = $request->has('conectar_kit_fracionamento_1') ? true : false;
            $modifiedFields['fechar_bomba_peristaltica'] = $request->has('fechar_bomba_peristaltica') ? true : false;
        
            $modifiedFields['pressionar_botao_pinch_close'] = $request->has('pressionar_botao_pinch_close') ? true : false;
            $modifiedFields['conectar_kit_fracionamento_2'] = $request->has('conectar_kit_fracionamento_2') ? true : false;
            $modifiedFields['prender_capilares_parede_theodorico'] = $request->has('prender_capilares_parede_theodorico') ? true : false;
            $modifiedFields['conectar_filtros_millex_gs'] = $request->has('conectar_filtros_millex_gs') ? true : false;
            $modifiedFields['verificar_linhas_conectadas'] = $request->has('verificar_linhas_conectadas') ? true : false;
            $modifiedFields['verificar_conexoes_capilares'] = $request->has('verificar_conexoes_capilares') ? true : false;
            $modifiedFields['verificar_agulha_succao'] = $request->has('verificar_agulha_succao') ? true : false;
            $modifiedFields['fechar_porta'] = $request->has('fechar_porta') ? true : false;
            $modifiedFields['programar_fracionamento_software'] = $request->has('programar_fracionamento_software') ? true : false;
            $modifiedFields['imprimir_etiqueta_frascos'] = $request->has('imprimir_etiqueta_frascos') ? true : false;
            $modifiedFields['alimentar_antecamara_frascos'] = $request->has('alimentar_antecamara_frascos') ? true : false;
            $modifiedFields['marcar_posicao_frascos'] = $request->has('marcar_posicao_frascos') ? true : false;
            $modifiedFields['pressionar_botao_from_synt'] = $request->has('pressionar_botao_from_synt') ? true : false;
            $modifiedFields['pressionar_botao_bulk_dilution'] = $request->has('pressionar_botao_bulk_dilution') ? true : false;
            $modifiedFields['pressionar_botao_start'] = $request->has('pressionar_botao_start') ? true : false;
            $modifiedFields['id_usuario_verificado_p8'] = $request->id_usuario_verificado_p8;
            
            $modifiedFields['atividade_fdg_18f'] = $request->atividade_fdg_18f;
            $modifiedFields['volume_soro_fisiologico'] = $request->volume_soro_fisiologico;
            $modifiedFields['imprimir_anexar_relatorio_producao'] = $request->has('imprimir_anexar_relatorio_producao') ? true : false;
            $modifiedFields['hora_inicio_p8'] = $request->hora_inicio_p8;
            $modifiedFields['hora_final_p8'] = $request->hora_final_p8;
            $modifiedFields['id_usuario_fracionamento_executado'] = $request->id_usuario_fracionamento_executado;
        
            $modifiedFields['ocorrencias_p9'] = $request->ocorrencias_p9;
            $modifiedFields['ocorrencias_horario_p9'] = $request->ocorrencias_horario_p9;
            $modifiedFields['id_usuario_execucao_ocorrencias_p9'] = $request->id_usuario_execucao_ocorrencias_p9;
            $modifiedFields['id_usuario_verificacao_ocorrencias_p9'] = $request->id_usuario_verificacao_ocorrencias_p9;
        }

        if ($request->action == 'partialExpedicao') {
            $modifiedFields['embalagem_balde_qtd'] = $request->embalagem_balde_qtd;
            $modifiedFields['embalagem_balde_separado'] = $request->embalagem_balde_separado;
            $modifiedFields['embalagem_balde_conferido'] = $request->has('embalagem_balde_conferido') ? true : false;
            $modifiedFields['embalagem_case_qtd'] = $request->embalagem_case_qtd;
            $modifiedFields['embalagem_case_separado'] = $request->embalagem_case_separado;
            $modifiedFields['embalagem_case_conferido'] = $request->has('embalagem_case_conferido') ? true : false;
            $modifiedFields['etiquetas_it_qtd'] = $request->etiquetas_it_qtd;
            $modifiedFields['etiquetas_it_separado'] = $request->etiquetas_it_separado;
            $modifiedFields['etiquetas_it_conferido'] = $request->has('etiquetas_it_conferido') ? true : false;
            $modifiedFields['bulas_fdg_qtd'] = $request->bulas_fdg_qtd;
            $modifiedFields['bulas_fdg_separado'] = $request->bulas_fdg_separado;
            $modifiedFields['bulas_fdg_conferido'] = $request->has('bulas_fdg_conferido') ? true : false;
            $modifiedFields['id_usuario_separado_embalagem_p10'] = $request->id_usuario_separado_embalagem_p10;
            $modifiedFields['horario_separado_embalagem_p10'] = $request->horario_separado_embalagem_p10;
            $modifiedFields['id_usuario_conferido_embalagem_p10'] = $request->id_usuario_conferido_embalagem_p10;
            $modifiedFields['horario_conferido_embalagem_p10'] = $request->horario_conferido_embalagem_p10;

            $modifiedFields['decl_exped_qtd'] = $request->decl_exped_qtd;
            $modifiedFields['decl_exped_separado'] = $request->decl_exped_separado;
            $modifiedFields['decl_exped_conferido'] = $request->has('decl_exped_conferido') ? true : false;
            $modifiedFields['ficha_emerg_qtd'] = $request->ficha_emerg_qtd;
            $modifiedFields['ficha_emerg_separado'] = $request->ficha_emerg_separado;
            $modifiedFields['ficha_emerg_conferido'] = $request->has('ficha_emerg_conferido') ? true : false;
            $modifiedFields['nota_fiscal_qtd'] = $request->nota_fiscal_qtd;
            $modifiedFields['nota_fiscal_separado'] = $request->nota_fiscal_separado;
            $modifiedFields['nota_fiscal_conferido'] = $request->has('nota_fiscal_conferido') ? true : false;
            $modifiedFields['termo_doacao_qtd'] = $request->termo_doacao_qtd;
            $modifiedFields['termo_doacao_separado'] = $request->termo_doacao_separado;
            $modifiedFields['termo_doacao_conferido'] = $request->has('termo_doacao_conferido') ? true : false;
            $modifiedFields['ident_veiculo_qtd'] = $request->ident_veiculo_qtd;
            $modifiedFields['ident_veiculo_separado'] = $request->ident_veiculo_separado;
            $modifiedFields['ident_veiculo_conferido'] = $request->has('ident_veiculo_conferido') ? true : false;
            $modifiedFields['form_tam_qtd'] = $request->form_tam_qtd;
            $modifiedFields['form_tam_separado'] = $request->form_tam_separado;
            $modifiedFields['form_tam_conferido'] = $request->has('form_tam_conferido') ? true : false;
            $modifiedFields['form_iata_qtd'] = $request->form_iata_qtd;
            $modifiedFields['form_iata_separado'] = $request->form_iata_separado;
            $modifiedFields['form_iata_conferido'] = $request->has('form_iata_conferido') ? true : false;
            $modifiedFields['id_usuario_separado_expedicao_p10'] = $request->id_usuario_separado_expedicao_p10;
            $modifiedFields['horario_separado_expedicao_p10'] = $request->horario_separado_expedicao_p10;
            $modifiedFields['id_usuario_conferido_expedicao_p10'] = $request->id_usuario_conferido_expedicao_p10;
            $modifiedFields['horario_conferido_expedicao_p10'] = $request->horario_conferido_expedicao_p10;
            
            $modifiedFields['horario_final_emb_exped'] = $request->horario_final_emb_exped;
            $modifiedFields['id_usuario_execucao_p10'] = $request->id_usuario_execucao_p10;
            $modifiedFields['id_usuario_verificacao_p10'] = $request->id_usuario_verificacao_p10;

            $modifiedFields['ocorrencias_p10'] = $request->ocorrencias_p10;
            $modifiedFields['ocorrencias_horario_p10'] = $request->ocorrencias_horario_p10;
            $modifiedFields['id_usuario_execucao_ocorrencias_p10'] = $request->id_usuario_execucao_ocorrencias_p10;
            $modifiedFields['id_usuario_verificacao_ocorrencias_p10'] = $request->id_usuario_verificacao_ocorrencias_p10;
        }

        if ($request->action == 'partialCQFQ') {
            $modifiedFields['aspecto_resultado'] = $request->aspecto_resultado;
            $modifiedFields['aspecto_data'] = $request->aspecto_data;
            $modifiedFields['id_usuario_aspecto'] = $request->id_usuario_aspecto;
            $modifiedFields['ph_1_resultado'] = $request->ph_1_resultado;
            $modifiedFields['ph_1_data'] = $request->ph_1_data;
            $modifiedFields['id_usuario_ph_1'] = $request->id_usuario_ph_1;
            $modifiedFields['ph_2_resultado'] = $request->ph_2_resultado;
            $modifiedFields['ph_2_data'] = $request->ph_2_data;
            $modifiedFields['id_usuario_ph_2'] = $request->id_usuario_ph_2;
            $modifiedFields['pureza_radionuclidica_1_resultado'] = $request->pureza_radionuclidica_1_resultado;
            $modifiedFields['pureza_radionuclidica_1_data'] = $request->pureza_radionuclidica_1_data;
            $modifiedFields['id_usuario_pureza_radionuclidica_1'] = $request->id_usuario_pureza_radionuclidica_1;
            $modifiedFields['pureza_radionuclidica_2_resultado'] = $request->pureza_radionuclidica_2_resultado;
            $modifiedFields['pureza_radionuclidica_2_data'] = $request->pureza_radionuclidica_2_data;
            $modifiedFields['id_usuario_pureza_radionuclidica_2'] = $request->id_usuario_pureza_radionuclidica_2;
            $modifiedFields['meia_vida_resultado'] = $request->meia_vida_resultado;
            $modifiedFields['meia_vida_data'] = $request->meia_vida_data;
            $modifiedFields['id_usuario_meia_vida'] = $request->id_usuario_meia_vida;
            $modifiedFields['solventes_resultado'] = $request->solventes_resultado;
            $modifiedFields['solventes_data'] = $request->solventes_data;
            $modifiedFields['id_usuario_solventes'] = $request->id_usuario_solventes;

            $modifiedFields['ocorrencias_p11'] = $request->ocorrencias_p11;
            $modifiedFields['id_usuario_verificacao_ocorrencias_p11'] = $request->id_usuario_verificacao_ocorrencias_p11;

            $modifiedFields['pureza_radioquimica_a_codigo'] = $request->pureza_radioquimica_a_codigo;
            $modifiedFields['pureza_radioquimica_a_resultado'] = $request->pureza_radioquimica_a_resultado;
            $modifiedFields['pureza_radioquimica_a_data'] = $request->pureza_radioquimica_a_data;
            $modifiedFields['id_usuario_pureza_radioquimica_a'] = $request->id_usuario_pureza_radioquimica_a;
            $modifiedFields['pureza_radioquimica_b_resultado'] = $request->pureza_radioquimica_b_resultado;
            $modifiedFields['pureza_radioquimica_b_data'] = $request->pureza_radioquimica_b_data;
            $modifiedFields['id_usuario_pureza_radioquimica_b'] = $request->id_usuario_pureza_radioquimica_b;
            $modifiedFields['pureza_quimica_resultado'] = $request->pureza_quimica_resultado;
            $modifiedFields['pureza_quimica_data'] = $request->pureza_quimica_data;
            $modifiedFields['id_usuario_pureza_quimica'] = $request->id_usuario_pureza_quimica;
            
            $modifiedFields['ocorrencias_p12'] = $request->ocorrencias_p12;
            $modifiedFields['id_usuario_verificacao_ocorrencias_p12'] = $request->id_usuario_verificacao_ocorrencias_p12;
            
            $modifiedFields['aprovacao_fisico_quimico'] = $request->aprovacao_fisico_quimico;
            $modifiedFields['data_aprovacao_fisico_quimico'] = $request->data_aprovacao_fisico_quimico;
            $modifiedFields['id_usuario_aprovacao_fisico_quimico'] = $request->id_usuario_aprovacao_fisico_quimico;
        }

        if ($request->action == 'partialCQM') {
            $modifiedFields['endotoxinas_codigo'] = $request->endotoxinas_codigo;
            $modifiedFields['endotoxinas_1_resultado'] = $request->endotoxinas_1_resultado;
            $modifiedFields['endotoxinas_2_resultado'] = $request->endotoxinas_2_resultado;
            $modifiedFields['endotoxinas_3_resultado'] = $request->endotoxinas_3_resultado;
            $modifiedFields['endotoxinas_4_resultado'] = $request->endotoxinas_4_resultado;
            $modifiedFields['endotoxinas_data'] = $request->endotoxinas_data;
            $modifiedFields['id_usuario_endotoxinas'] = $request->id_usuario_endotoxinas;
            $modifiedFields['codigo_calibracao_pts'] = $request->codigo_calibracao_pts;
            $modifiedFields['lote_cartucho_pts'] = $request->lote_cartucho_pts;

            $modifiedFields['membrana_equipamento'] = $request->membrana_equipamento;
            $modifiedFields['membrana_lote'] = $request->membrana_lote;
            $modifiedFields['membrana_validade'] = $request->membrana_validade;
            $modifiedFields['id_usuario_membrana'] = $request->id_usuario_membrana;

            $modifiedFields['pressao_teste_bolha_fornecida'] = $request->pressao_teste_bolha_fornecida;
            $modifiedFields['pressao_teste_bolha_obtida'] = $request->pressao_teste_bolha_obtida;
            $modifiedFields['id_usuario_pressao_teste_bolha'] = $request->id_usuario_pressao_teste_bolha;

            $modifiedFields['ocorrencias_p13'] = $request->ocorrencias_p13;
            $modifiedFields['id_usuario_verificacao_ocorrencias_p13'] = $request->id_usuario_verificacao_ocorrencias_p13;
            
            $modifiedFields['aprovacao_microbiologico'] = $request->aprovacao_microbiologico;
            $modifiedFields['data_aprovacao_microbiologico'] = $request->data_aprovacao_microbiologico;
            $modifiedFields['id_usuario_aprovacao_microbiologico'] = $request->id_usuario_aprovacao_microbiologico;

            $modifiedFields['esterilidade_data_inicio_analise'] = $request->esterilidade_data_inicio_analise;
            $modifiedFields['id_usuario_esterilidade'] = $request->id_usuario_esterilidade;

            $modifiedFields['esterilidade_codigo'] = $request->esterilidade_codigo;
            $modifiedFields['esterilidade_1_resultado'] = $request->esterilidade_1_resultado;
            $modifiedFields['esterilidade_1_data'] = $request->esterilidade_1_data;
            $modifiedFields['id_usuario_esterilidade_1'] = $request->id_usuario_esterilidade_1;
            $modifiedFields['esterilidade_2_resultado'] = $request->esterilidade_2_resultado;
            $modifiedFields['esterilidade_2_data'] = $request->esterilidade_2_data;
            $modifiedFields['id_usuario_esterilidade_2'] = $request->id_usuario_esterilidade_2;
            $modifiedFields['esterilidade_3_resultado'] = $request->esterilidade_3_resultado;
            $modifiedFields['esterilidade_3_data'] = $request->esterilidade_3_data;
            $modifiedFields['id_usuario_esterilidade_3'] = $request->id_usuario_esterilidade_3;

            $modifiedFields['ocorrencias_p14'] = $request->ocorrencias_p14;
            
            $modifiedFields['aprovacao_esterilidade'] = $request->aprovacao_esterilidade;
            $modifiedFields['data_aprovacao_esterilidade'] = $request->data_aprovacao_esterilidade;
            $modifiedFields['id_usuario_aprovacao_esterilidade'] = $request->id_usuario_aprovacao_esterilidade;
        }

        if ($request->action == 'totalAprovacao') {
            // Check if user is authenticated and their password matches the provided one
            $user = User::find(Auth::id());
            
            if (!Hash::check($request->password, $user->password))
            return redirect()->back()->with('alert-danger', 'Senha incorreta.')->with('modal', '#confirmModal')->withInput();
        
            $modifiedFields['id_usuario_supervisor_controle_qualidade'] = $request->id_usuario_supervisor_controle_qualidade;
            $modifiedFields['atendimento_criterios'] = $request->atendimento_criterios;
            $modifiedFields['aprovacao_lote'] = $request->aprovacao_lote;
            $modifiedFields['id_usuario_resposavel_garantia_qualidade'] = Auth::id();
            $modifiedFields['hora_emissao_laudo'] = $request->hora_emissao_laudo;
            
            $modifiedFields['completed'] = true;

            DB::beginTransaction();

            $registro_lote = Registro_Lote::UpdateOrCreate(
                ['lote' => $request->lote],
                $modifiedFields
            );

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Finalizar Registro de Lote')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Registro de Lote Finalizado:\n" .
                "- ID: {$registro_lote->id}\n" .
                "- Lote: {$registro_lote->lote}\n";

            $log->save();

            DB::commit();
        }
        
        else {
            $registro_lote = Registro_Lote::UpdateOrCreate(
                ['lote' => $request->lote],
                $modifiedFields
            );
        }

        
        return redirect()->back()->with('alert-success', 'Registro de lote salvo com sucesso.')->withInput();
    }
    
    public function make_pdf(Request $request){
        $registro_lote = Registro_Lote::where('lote', $request->lote)->get()[0];

        $pdf = new FPDI();
        
        $pdfPath = './Registro_de_Lote_Layout.pdf';
        $pageCount = $pdf->setSourceFile($pdfPath);

        // Definir fonte e cor
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 255);

        $lote = $registro_lote->lote;
        list($ano, $mes, $dia) = explode("-", $registro_lote->data_fabricacao);

        // PÁGINA 1
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

        // PÁGINA 2
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(2);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

        // PÁGINA 3
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(3);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            $pdf->SetXY(100, 95);
            $pdf->Write(10, $registro_lote->lote_agua_enriquecida);
            
            if ($registro_lote->id_usuario_lote_agua_enriquecida){
                $pdf->SetXY(58, 101);
                $pdf->Write(10, User::find($registro_lote->id_usuario_lote_agua_enriquecida)->username . " - " . $registro_lote->id_usuario_lote_agua_enriquecida);
            }

            // ---------------------------------------------------//

            if ($registro_lote->pressao_ar_comprimido){
                $pdf->SetXY(150, 120);
                $pdf->Write(10, $registro_lote->pressao_ar_comprimido . " bar");
            }
            if ($registro_lote->pressao_H){
                $pdf->SetXY(150, 128);
                $pdf->Write(10, $registro_lote->pressao_H . " bar");
            }
            if ($registro_lote->pressao_He_refrigeracao){
                $pdf->SetXY(150, 134);
                $pdf->Write(10, $registro_lote->pressao_He_refrigeracao . " bar");
            }
            if ($registro_lote->pressao_He_analitico){
                $pdf->SetXY(150, 140);
                $pdf->Write(10, $registro_lote->pressao_He_analitico . " bar");
            }
            if ($registro_lote->radiacao_ambiental_lab){
                $pdf->SetXY(150, 147.5);
                $pdf->Write(10, $registro_lote->radiacao_ambiental_lab . " " . chr(181) . "Sv/h");
            }
            if ($registro_lote->id_usuario_verificacao_p3){
                $pdf->SetXY(58, 155.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_p3)->username . " - " . $registro_lote->id_usuario_verificacao_p3);
            }

            // ---------------------------------------------------//

            $pdf->SetXY(30, 181.5);
            $pdf->Write(10, substr($registro_lote->hora_inicio_irradiacao_agua_enriquecida, 0, -3));

            $pdf->SetXY(56, 181.5);
            $pdf->Write(10, substr($registro_lote->hora_final_irradiacao_agua_enriquecida, 0, -3));

            if ($registro_lote->ativ_teorica_F18){
                $pdf->SetXY(84, 181.5);
                $pdf->Write(10, substr($registro_lote->ativ_teorica_F18, 0, -3) . " mCi");
            }
            if ($registro_lote->id_usuario_irradiacao_agua_enriquecida){
                $pdf->SetXY(127, 178);
                $pdf->Write(10, User::find($registro_lote->id_usuario_irradiacao_agua_enriquecida)->username . " - " . $registro_lote->id_usuario_irradiacao_agua_enriquecida);
            }

            // ---------------------------------------------------//

            $pdf->SetXY(30, 205);
            $pdf->Write(10, substr($registro_lote->hora_inicio_transferir_F18_sintese, 0, -3));

            $pdf->SetXY(76.5, 205);
            $pdf->Write(10, substr($registro_lote->hora_final_transferir_F18_sintese, 0, -3));
            
            if ($registro_lote->id_usuario_transferir_F18_sintese){
                $pdf->SetXY(126, 203);
                $pdf->Write(10, User::find($registro_lote->id_usuario_transferir_F18_sintese)->username . " - " . $registro_lote->id_usuario_transferir_F18_sintese);
            }

            // ---------------------------------------------------//

            $pdf->SetXY(56, 218);

            $texto = utf8_decode($registro_lote->ocorrencias_p3);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;

            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 6, trim($restante), 0, 'L');
            }

            $pdf->SetXY(47, 241);
            $pdf->Write(10, substr($registro_lote->ocorrencias_horario_p3, 0, -3));
            
            if ($registro_lote->id_usuario_ocorrencias_p3){
                $pdf->SetXY(110, 241);
                $pdf->Write(10, User::find($registro_lote->id_usuario_ocorrencias_p3)->username . " - " . $registro_lote->id_usuario_ocorrencias_p3);
            }

            // ---------------------------------------------------//
            if ($registro_lote->logbook_anexado === true) {
                $pdf->SetXY(105, 252.5);
                $pdf->Write(10, "X");

                $pdf->SetXY(42, 259);
                $pdf->Write(10, date("m/d/Y", strtotime($registro_lote->logbook_data)));

                $pdf->SetXY(91, 259);
                $pdf->Write(10, substr($registro_lote->logbook_time, 0, -3));
                
                if ($registro_lote->id_usuario_logbook){
                    $pdf->SetXY(140, 259);
                    $pdf->Write(10, User::find($registro_lote->id_usuario_logbook)->username . " - " . $registro_lote->id_usuario_logbook);
                }
            } else if ($registro_lote->logbook_anexado === false) {
                $pdf->SetXY(151.5, 252.5);
                $pdf->Write(10, "X");
            }

        // PÁGINA 4
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(4);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            if ($registro_lote->modulo_sintese === false){
                $pdf->SetXY(100.5, 77 );
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->modulo_sintese === true){
                $pdf->SetXY(100.5, 84);
                $pdf->Write(10, "X");
            }

            // ---------------------------------------------------//

            $pdf->SetXY(111.5, 117);
            $pdf->Write(10, $registro_lote->kryptofix222_lote);

            if ($registro_lote->kryptofix222_data_validade){
                $pdf->SetXY(168, 117);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->kryptofix222_data_validade)));
            }

            $pdf->SetXY(111.5, 124.5);
            $pdf->Write(10, $registro_lote->triflato_manose_lote);
            
            if ($registro_lote->triflato_manose_data_validade){
                $pdf->SetXY(168, 124.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->triflato_manose_data_validade)));
            }

            $pdf->SetXY(111.5, 132);
            $pdf->Write(10, $registro_lote->hidroxido_sodio_lote);
            
            if ($registro_lote->hidroxido_sodio_data_validade){
                $pdf->SetXY(168, 132);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->hidroxido_sodio_data_validade)));
            }

            $pdf->SetXY(111.5, 139);
            $pdf->Write(10, $registro_lote->agua_injetaveis_lote);
            
            if ($registro_lote->agua_injetaveis_data_validade){
                $pdf->SetXY(168, 139);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agua_injetaveis_data_validade)));
            }

            $pdf->SetXY(111.5, 146);
            $pdf->Write(10, $registro_lote->acetronitrila_anidra_lote);
            
            if ($registro_lote->acetronitrila_anidra_data_validade){
                $pdf->SetXY(168, 146);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->acetronitrila_anidra_data_validade)));
            }

            $pdf->SetXY(111.5, 153);
            $pdf->Write(10, $registro_lote->ifp_synthera_lote);
            
            if ($registro_lote->ifp_synthera_data_validade){
                $pdf->SetXY(168, 153);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->ifp_synthera_data_validade)));
            }

            // ---------------------------------------------------//

            $pdf->SetXY(111.5, 176);
            $pdf->Write(10, $registro_lote->sep_pak_lote);
            
            if ($registro_lote->sep_pak_data_validade){
                $pdf->SetXY(168, 176);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->sep_pak_data_validade)));
            }

            $pdf->SetXY(111.5, 183.5);
            $pdf->Write(10, $registro_lote->coluna_scx_lote);
            
            if ($registro_lote->coluna_scx_data_validade){
                $pdf->SetXY(168, 183.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->coluna_scx_data_validade)));
            }

            $pdf->SetXY(111.5, 190.5);
            $pdf->Write(10, $registro_lote->coluna_c18_lote);
            
            if ($registro_lote->coluna_c18_data_validade){
                $pdf->SetXY(168, 190.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->coluna_c18_data_validade)));
            }

            $pdf->SetXY(111.5, 197.5);
            $pdf->Write(10, $registro_lote->coluna_alumina_lote);
            
            if ($registro_lote->coluna_alumina_data_validade){
                $pdf->SetXY(168, 197.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->coluna_alumina_data_validade)));
            }

            $pdf->SetXY(111.5, 205);
            $pdf->Write(10, $registro_lote->seringa_3ml_lote);
            
            if ($registro_lote->seringa_3ml_data_validade){
                $pdf->SetXY(168, 205);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->seringa_3ml_data_validade)));
            }

            $pdf->SetXY(111.5, 212);
            $pdf->Write(10, $registro_lote->agulha_05x25_lote);
            
            if ($registro_lote->agulha_05x25_data_validade){
                $pdf->SetXY(168, 212);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agulha_05x25_data_validade)));
            }

            $pdf->SetXY(111.5, 219.5);
            $pdf->Write(10, $registro_lote->agua_injetavel_seringa_lote);
            
            if ($registro_lote->agua_injetavel_seringa_data_validade){
                $pdf->SetXY(168, 219.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agua_injetavel_seringa_data_validade)));
            }

            $pdf->SetXY(111.5, 226.5);
            $pdf->Write(10, $registro_lote->etanol_seringa_lote);
            
            if ($registro_lote->etanol_seringa_data_validade){
                $pdf->SetXY(168, 226.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->etanol_seringa_data_validade)));
            }

            $pdf->SetXY(111.5, 233.5);
            $pdf->Write(10, $registro_lote->NaHCO3_seringa_lote);
            
            if ($registro_lote->NaHCO3_seringa_data_validade){
                $pdf->SetXY(168, 233.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->NaHCO3_seringa_data_validade)));
            }

            // ---------------------------------------------------//
            
            if ($registro_lote->id_usuario_separado_registrado_p4){
                $pdf->SetXY(82, 241);
                $pdf->Write(10, User::find($registro_lote->id_usuario_separado_registrado_p4)->username . " - " . $registro_lote->id_usuario_separado_registrado_p4);
            }
            
            if ($registro_lote->data_separado_registrado_p4){
                $pdf->SetXY(165, 241);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->data_separado_registrado_p4)));
            }

            if ($registro_lote->id_usuario_recebido_conferido_p4){
                $pdf->SetXY(82, 248.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_recebido_conferido_p4)->username . " - " . $registro_lote->id_usuario_recebido_conferido_p4);
            }

            if ($registro_lote->data_recebido_conferido_p4){
                $pdf->SetXY(165, 248.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->data_recebido_conferido_p4)));
            }
            
        // PÁGINA 5
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(5);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            $pdf->SetXY(30, 82.5);
            $pdf->Write(10, substr($registro_lote->hora_inicio_montagem_kit_synthera, 0, -3));

            $pdf->SetXY(62, 82.5);
            $pdf->Write(10, substr($registro_lote->hora_final_montagem_kit_synthera, 0, -3));

            if ($registro_lote->id_usuario_execucao_montagem_kit_synthera){
                $pdf->SetXY(98, 80.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_execucao_montagem_kit_synthera)->username . " - " . $registro_lote->id_usuario_execucao_montagem_kit_synthera);
            }

            if ($registro_lote->id_usuario_verificacao_montagem_kit_synthera){
                $pdf->SetXY(146, 80.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_montagem_kit_synthera)->username . " - " . $registro_lote->id_usuario_verificacao_montagem_kit_synthera);
            }
            // ---------------------------------------------------//

            $pdf->SetXY(138, 106);
            $pdf->Write(10, $registro_lote->temperatura_lab_producao);

            $pdf->SetXY(138, 113.5);
            $pdf->Write(10, $registro_lote->umidade_lab_producao);

            if ($registro_lote->id_usuario_verificacao_p5){
                $pdf->SetXY(59, 121);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_p5)->username . " - " . $registro_lote->id_usuario_verificacao_p5);
            }

            // ---------------------------------------------------//
            
            if ($registro_lote->limpeza_celula){
                $pdf->SetXY(177, 154);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->verif_volume_H218O){
                $pdf->SetXY(177, 161);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->verif_frasco_rejeitos){
                $pdf->SetXY(177, 168);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->verif_bolsa_ar){
                $pdf->SetXY(177, 175.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->abrir_valvula_ar_comprimido){
                $pdf->SetXY(177, 182.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->abrir_valvula_nitrogenio){
                $pdf->SetXY(177, 189.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->verif_pos_capilares){
                $pdf->SetXY(177, 197);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->ligar_controle_synthera){
                $pdf->SetXY(177, 204);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->ligar_notebook_synthera){
                $pdf->SetXY(177, 211);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->iniciar_programa_mpb){
                $pdf->SetXY(177, 218.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->retirar_ifp_usado){
                $pdf->SetXY(177, 225.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->inserir_ifp_synthera){
                $pdf->SetXY(177, 232.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->conectar_theodorico){
                $pdf->SetXY(177, 240);
                $pdf->Write(10, "X");
            }

        // PÁGINA 6
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(6);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//
        
            if ($registro_lote->iniciar_auto_teste){
                $pdf->SetXY(177, 69);
                $pdf->Write(10, "X");
            }
        
            if ($registro_lote->efetuar_diluicao_triflato_manose){
                $pdf->SetXY(177, 76);
                $pdf->Write(10, "X");
            }
        
            if ($registro_lote->remover_bloco_vermelho){
                $pdf->SetXY(177, 83);
                $pdf->Write(10, "X");
            }
        
            if ($registro_lote->fechar_portas_bbs){
                $pdf->SetXY(177, 90);
                $pdf->Write(10, "X");
            }
        
            if ($registro_lote->pressionar_start){
                $pdf->SetXY(177, 98.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->id_usuario_verificacao_acoes){
                $pdf->SetXY(58, 106.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_acoes)->username . " - " . $registro_lote->id_usuario_verificacao_acoes);
            }

            // ---------------------------------------------------//

            if ($registro_lote->ativ_chegada_18F){
                $pdf->SetXY(30, 163.5);
                $pdf->Write(10, substr($registro_lote->ativ_chegada_18F, 0, -3) . " mCi");
            }
            if ($registro_lote->ativ_residual_18F){
                $pdf->SetXY(72.5, 163.5);
                $pdf->Write(10, substr($registro_lote->ativ_residual_18F, 0, -3) . " mCi");
            }
            if ($registro_lote->ativ_modulo_sintese){
                $pdf->SetXY(110, 163.5);
                $pdf->Write(10, substr($registro_lote->ativ_modulo_sintese, 0, -3) . " mCi");
            }
            if ($registro_lote->ativ_modulo_fracionamento){
                $pdf->SetXY(148, 163.5);
                $pdf->Write(10, substr($registro_lote->ativ_modulo_fracionamento, 0, -3) . " mCi");
            }
            if ($registro_lote->hora_inicio_sintese){
                $pdf->SetXY(30, 178.5);
                $pdf->Write(10, substr($registro_lote->hora_inicio_sintese, 0, -3) . " h");
            }
            if ($registro_lote->hora_final_sintese){
                $pdf->SetXY(72.5, 178.5);
                $pdf->Write(10, substr($registro_lote->hora_final_sintese, 0, -3) . " h");
            }
            if ($registro_lote->rendimento_sintese){
                $pdf->SetXY(148, 174.5);
                $pdf->Write(10, substr($registro_lote->rendimento_sintese, 0, -3) . " %");
            }
            if ($registro_lote->id_usuario_execucao_p6){
                $pdf->SetXY(61, 186.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_execucao_p6)->username . " - " . $registro_lote->id_usuario_execucao_p6);
            }
            if ($registro_lote->id_usuario_verificacao_p6){
                $pdf->SetXY(141, 186.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_p6)->username . " - " . $registro_lote->id_usuario_verificacao_p6);
            }

            // ---------------------------------------------------//

            $pdf->SetXY(56, 207);

            $texto = utf8_decode($registro_lote->ocorrencias_p6);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;

            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            $pdf->SetXY(30, 262);
            $pdf->Write(10, substr($registro_lote->ocorrencias_horario_p6, 0, -3));

            if ($registro_lote->id_usuario_execucao_ocorrencias_p6){
                $pdf->SetXY(63, 260);
                $pdf->Write(10, User::find($registro_lote->id_usuario_execucao_ocorrencias_p6)->username . " - " . $registro_lote->id_usuario_execucao_ocorrencias_p6);
            }

            if ($registro_lote->id_usuario_verificacao_ocorrencias_p6){
                $pdf->SetXY(127, 260);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_ocorrencias_p6)->username . " - " . $registro_lote->id_usuario_verificacao_ocorrencias_p6);
            }

        // PÁGINA 7
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(7);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            $pdf->SetXY(110, 99);
            $pdf->Write(10, $registro_lote->kit_fracionamento_1_lote);

            if ($registro_lote->kit_fracionamento_1_data_validade){
                $pdf->SetXY(160, 99);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->kit_fracionamento_1_data_validade)));
            }

            $pdf->SetXY(110, 106.5);
            $pdf->Write(10, $registro_lote->kit_fracionamento_2_lote);

            if ($registro_lote->kit_fracionamento_2_data_validade){
                $pdf->SetXY(160, 106.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->kit_fracionamento_2_data_validade)));
            }

            $pdf->SetXY(110, 115);
            $pdf->Write(10, $registro_lote->filtro_millex_gs_lote);

            if ($registro_lote->filtro_millex_gs_data_validade){
                $pdf->SetXY(160, 115);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->filtro_millex_gs_data_validade)));
            }

            $pdf->SetXY(110, 123.5);
            $pdf->Write(10, $registro_lote->filtro_millex_gv_lote);

            if ($registro_lote->filtro_millex_gv_data_validade){
                $pdf->SetXY(160, 123.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->filtro_millex_gv_data_validade)));
            }

            $pdf->SetXY(110, 131);
            $pdf->Write(10, $registro_lote->soro_fisiologico_lote);

            if ($registro_lote->soro_fisiologico_data_validade){
                $pdf->SetXY(160, 131);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->soro_fisiologico_data_validade)));
            }

            $pdf->SetXY(110, 138.5);
            $pdf->Write(10, $registro_lote->agulha_09x40_lote);

            if ($registro_lote->agulha_09x40_data_validade){
                $pdf->SetXY(160, 138.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agulha_09x40_data_validade)));
            }

            $pdf->SetXY(98.5, 146.5);
            $pdf->Write(10, $registro_lote->frascos_15ml_qtd);

            $pdf->SetXY(110, 146.5);
            $pdf->Write(10, $registro_lote->frascos_15ml_lote);

            if ($registro_lote->frascos_15ml_data_validade){
                $pdf->SetXY(160, 146.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->frascos_15ml_data_validade)));
            }

            $pdf->SetXY(110, 155.5);
            $pdf->Write(10, $registro_lote->frascos_bulk_lote);

            if ($registro_lote->frascos_bulk_data_validade){
                $pdf->SetXY(160, 155.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->frascos_bulk_data_validade)));
            }

            // ---------------------------------------------------//

            if ($registro_lote->id_usuario_separado_registrado_p7){
                $pdf->SetXY(82, 164);
                $pdf->Write(10, User::find($registro_lote->id_usuario_separado_registrado_p7)->username . " - " . $registro_lote->id_usuario_separado_registrado_p7);
            }
            
            if ($registro_lote->data_separado_registrado_p7){
                $pdf->SetXY(156, 164);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->data_separado_registrado_p7)));
            }

            if ($registro_lote->id_usuario_recebido_conferido_p7){
                $pdf->SetXY(82, 171.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_recebido_conferido_p7)->username . " - " . $registro_lote->id_usuario_recebido_conferido_p7);
            }

            if ($registro_lote->data_recebido_conferido_p7){
                $pdf->SetXY(156, 171.5);
                $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->data_recebido_conferido_p7)));
            }

            // ---------------------------------------------------//

            if ($registro_lote->ligar_theodorico){
                $pdf->SetXY(170.5, 195);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->colocar_castelo_chumbo_dws){
                $pdf->SetXY(170.5, 202.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->pressionar_botao_park){
                $pdf->SetXY(170.5, 209.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->pressionar_botao_pinch_open){
                $pdf->SetXY(170.5, 217);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->retirar_kit_usado){
                $pdf->SetXY(170.5, 224);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->realizar_limpeza_theodorico){
                $pdf->SetXY(170.5, 231);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->conectar_capilares_synthera_bulk){
                $pdf->SetXY(170.5, 238);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->conectar_kit_fracionamento_1){
                $pdf->SetXY(170.5, 245.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->fechar_bomba_peristaltica){
                $pdf->SetXY(170.5, 253);
                $pdf->Write(10, "X");
            }
    
        // PÁGINA 8
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(8);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            if ($registro_lote->pressionar_botao_pinch_close){
                $pdf->SetXY(170.5, 69);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->conectar_kit_fracionamento_2){
                $pdf->SetXY(170.5, 76);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->prender_capilares_parede_theodorico){
                $pdf->SetXY(170.5, 83.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->conectar_filtros_millex_gs){
                $pdf->SetXY(170.5, 91.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->verificar_linhas_conectadas){
                $pdf->SetXY(170.5, 99.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->verificar_conexoes_capilares){
                $pdf->SetXY(170.5, 107.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->verificar_agulha_succao){
                $pdf->SetXY(170.5, 115.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->fechar_porta){
                $pdf->SetXY(170.5, 123);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->programar_fracionamento_software){
                $pdf->SetXY(170.5, 130);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->imprimir_etiqueta_frascos){
                $pdf->SetXY(170.5, 137.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->alimentar_antecamara_frascos){
                $pdf->SetXY(170.5, 144.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->marcar_posicao_frascos){
                $pdf->SetXY(170.5, 151.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->pressionar_botao_from_synt){
                $pdf->SetXY(170.5, 159);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->pressionar_botao_bulk_dilution){
                $pdf->SetXY(170.5, 166);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->pressionar_botao_start){
                $pdf->SetXY(170.5, 173.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->id_usuario_verificado_p8){
                $pdf->SetXY(60, 180);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificado_p8)->username . " - " . $registro_lote->id_usuario_verificado_p8);
            }

            // ---------------------------------------------------//

            if ($registro_lote->hora_final_p8){
                $pdf->SetXY(103.5, 202.5);
                $pdf->Write(10, substr($registro_lote->atividade_fdg_18f, 0, -3) . " mCi");
            }

            if ($registro_lote->volume_soro_fisiologico){
                $pdf->SetXY(103.5, 210);
                $pdf->Write(10, substr($registro_lote->volume_soro_fisiologico, 0, -3) . " ml");
            }
            
            if ($registro_lote->imprimir_anexar_relatorio_producao){
                $pdf->SetXY(143, 217.5);
                $pdf->Write(10, "X");
            }
            
            if ($registro_lote->hora_inicio_p8){
                $pdf->SetXY(30, 232);
                $pdf->Write(10, substr($registro_lote->hora_inicio_p8, 0, -3) . " h");
            }

            if ($registro_lote->hora_final_p8){
                $pdf->SetXY(65, 232);
                $pdf->Write(10, substr($registro_lote->hora_final_p8, 0, -3) . " h");
            }

            if ($registro_lote->id_usuario_fracionamento_executado){
                $pdf->SetXY(104, 229.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_fracionamento_executado)->username . " - " . $registro_lote->id_usuario_fracionamento_executado);
            }

        // PÁGINA 9
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(9);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            $pdf->SetXY(62, 129);

            $texto = utf8_decode($registro_lote->ocorrencias_p9);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;
            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            $pdf->SetXY(30, 212.5);
            $pdf->Write(10, substr($registro_lote->ocorrencias_horario_p9, 0, -3));

            if ($registro_lote->id_usuario_execucao_ocorrencias_p9){
                $pdf->SetXY(60, 210);
                $pdf->Write(10, User::find($registro_lote->id_usuario_execucao_ocorrencias_p9)->username . " - " . $registro_lote->id_usuario_execucao_ocorrencias_p9);
            }

            if ($registro_lote->id_usuario_verificacao_ocorrencias_p9){
                $pdf->SetXY(124, 210);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_ocorrencias_p9)->username . " - " . $registro_lote->id_usuario_verificacao_ocorrencias_p9);
            }

        // PÁGINA 10
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(10);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            $pdf->SetXY(90, 81.5);
            $pdf->Write(10, $registro_lote->embalagem_balde_qtd);

            $pdf->SetXY(122, 81.5);
            $pdf->Write(10, $registro_lote->embalagem_balde_separado);
            
            if ($registro_lote->embalagem_balde_conferido){
                $pdf->SetXY(169, 81.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 88);
            $pdf->Write(10, $registro_lote->embalagem_case_qtd);

            $pdf->SetXY(122, 88);
            $pdf->Write(10, $registro_lote->embalagem_case_separado);

            if ($registro_lote->embalagem_case_conferido){
                $pdf->SetXY(169, 88);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 95);
            $pdf->Write(10, $registro_lote->etiquetas_it_qtd);

            $pdf->SetXY(122, 95);
            $pdf->Write(10, $registro_lote->etiquetas_it_separado);

            if ($registro_lote->etiquetas_it_conferido){
                $pdf->SetXY(169, 95);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 101.5);
            $pdf->Write(10, $registro_lote->bulas_fdg_qtd);

            $pdf->SetXY(122, 101.5);
            $pdf->Write(10, $registro_lote->bulas_fdg_separado);

            if ($registro_lote->bulas_fdg_conferido){
                $pdf->SetXY(169, 101.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->id_usuario_separado_embalagem_p10){
                $pdf->SetXY(59, 108);
                $pdf->Write(10, User::find($registro_lote->id_usuario_separado_embalagem_p10)->username . " - " . $registro_lote->id_usuario_separado_embalagem_p10);
            }

            $pdf->SetXY(166, 107.5);
            $pdf->Write(10, substr($registro_lote->horario_separado_embalagem_p10, 0, -3));

            if ($registro_lote->id_usuario_conferido_embalagem_p10){
                $pdf->SetXY(60, 115.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_conferido_embalagem_p10)->username . " - " . $registro_lote->id_usuario_conferido_embalagem_p10);
            }

            $pdf->SetXY(166, 114.5);
            $pdf->Write(10, substr($registro_lote->horario_conferido_embalagem_p10, 0, -3));

            // ---------------------------------------------------//

            $pdf->SetXY(90, 139.5);
            $pdf->Write(10, $registro_lote->decl_exped_qtd);

            $pdf->SetXY(122, 139.5);
            $pdf->Write(10, $registro_lote->decl_exped_separado);
            
            if ($registro_lote->decl_exped_conferido){
                $pdf->SetXY(169, 139.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 146);
            $pdf->Write(10, $registro_lote->ficha_emerg_qtd);

            $pdf->SetXY(122, 146);
            $pdf->Write(10, $registro_lote->ficha_emerg_separado);
            
            if ($registro_lote->ficha_emerg_conferido){
                $pdf->SetXY(169, 146);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 153);
            $pdf->Write(10, $registro_lote->nota_fiscal_qtd);

            $pdf->SetXY(122, 153);
            $pdf->Write(10, $registro_lote->nota_fiscal_separado);
            
            if ($registro_lote->nota_fiscal_conferido){
                $pdf->SetXY(169, 153);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 159.5);
            $pdf->Write(10, $registro_lote->termo_doacao_qtd);

            $pdf->SetXY(122, 159.5);
            $pdf->Write(10, $registro_lote->termo_doacao_separado);
            
            if ($registro_lote->termo_doacao_conferido){
                $pdf->SetXY(169, 159.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 166);
            $pdf->Write(10, $registro_lote->ident_veiculo_qtd);

            $pdf->SetXY(122, 166);
            $pdf->Write(10, $registro_lote->ident_veiculo_separado);
            
            if ($registro_lote->ident_veiculo_conferido){
                $pdf->SetXY(169, 166);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 173);
            $pdf->Write(10, $registro_lote->form_tam_qtd);

            $pdf->SetXY(122, 173);
            $pdf->Write(10, $registro_lote->form_tam_separado);
            
            if ($registro_lote->form_tam_conferido){
                $pdf->SetXY(169, 173);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(90, 179.5);
            $pdf->Write(10, $registro_lote->form_iata_qtd);

            $pdf->SetXY(122, 179.5);
            $pdf->Write(10, $registro_lote->form_iata_separado);
            
            if ($registro_lote->form_iata_conferido){
                $pdf->SetXY(169, 179.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->id_usuario_separado_expedicao_p10){
                $pdf->SetXY(59, 186);
                $pdf->Write(10, User::find($registro_lote->id_usuario_separado_expedicao_p10)->username . " - " . $registro_lote->id_usuario_separado_expedicao_p10);
            }

            $pdf->SetXY(166, 186);
            $pdf->Write(10, substr($registro_lote->horario_separado_expedicao_p10, 0, -3));

            if ($registro_lote->id_usuario_conferido_expedicao_p10){
                $pdf->SetXY(59, 193);
                $pdf->Write(10, User::find($registro_lote->id_usuario_conferido_expedicao_p10)->username . " - " . $registro_lote->id_usuario_conferido_expedicao_p10);
            }

            $pdf->SetXY(166, 193);
            $pdf->Write(10, substr($registro_lote->horario_conferido_expedicao_p10, 0, -3));

            // ---------------------------------------------------//

            if ($registro_lote->horario_final_emb_exped){
                $pdf->SetXY(30, 218);
                $pdf->Write(10, substr($registro_lote->horario_final_emb_exped, 0, -3) . " h");
            }

            if ($registro_lote->id_usuario_execucao_p10){
                $pdf->SetXY(59, 216);
                $pdf->Write(10, User::find($registro_lote->id_usuario_execucao_p10)->username . " - " . $registro_lote->id_usuario_execucao_p10);
            }

            if ($registro_lote->id_usuario_verificacao_p10){
                $pdf->SetXY(123, 216);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_p10)->username . " - " . $registro_lote->id_usuario_verificacao_p10);
            }

            // ---------------------------------------------------//

            $pdf->SetXY(57, 236.5);

            $texto = utf8_decode($registro_lote->ocorrencias_p10);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;
            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            $pdf->SetXY(30, 262);
            $pdf->Write(10, substr($registro_lote->ocorrencias_horario_p10, 0, -3));

            if ($registro_lote->id_usuario_execucao_ocorrencias_p10){
                $pdf->SetXY(62.5, 260);
                $pdf->Write(10, User::find($registro_lote->id_usuario_execucao_ocorrencias_p10)->username . " - " . $registro_lote->id_usuario_execucao_ocorrencias_p10);
            }

            if ($registro_lote->id_usuario_verificacao_ocorrencias_p10){
                $pdf->SetXY(126.5, 260);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_ocorrencias_p10)->username . " - " . $registro_lote->id_usuario_verificacao_ocorrencias_p10);
            }

        // PAGINA 11
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(11);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//
        
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(121.5, 99);
            $pdf->Write(10, $registro_lote->aspecto_resultado);

            $pdf->SetXY(144, 99);
            $pdf->Write(10, $registro_lote->aspecto_data);

            if ($registro_lote->id_usuario_aspecto){
                $pdf->SetXY(168, 99);
                $pdf->Write(10, User::find($registro_lote->id_usuario_aspecto)->username . " - " . $registro_lote->id_usuario_aspecto);
            }

            $pdf->SetXY(121.5, 114.5);
            $pdf->Write(10, $registro_lote->ph_1_resultado);

            $pdf->SetXY(144, 114.5);
            $pdf->Write(10, $registro_lote->ph_1_data);

            if ($registro_lote->id_usuario_ph_1){
                $pdf->SetXY(168, 114.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_ph_1)->username . " - " . $registro_lote->id_usuario_ph_1);
            }

            $pdf->SetXY(121.5, 124.5);
            $pdf->Write(10, $registro_lote->ph_2_resultado);

            $pdf->SetXY(144, 124.5);
            $pdf->Write(10, $registro_lote->ph_2_data);

            if ($registro_lote->id_usuario_ph_2){
                $pdf->SetXY(168, 124.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_ph_2)->username . " - " . $registro_lote->id_usuario_ph_2);
            }

            $pdf->SetXY(121.5, 140.5);
            $pdf->Write(10, $registro_lote->pureza_radionuclidica_1_resultado);

            $pdf->SetXY(144, 140.5);
            $pdf->Write(10, $registro_lote->pureza_radionuclidica_1_data);

            if ($registro_lote->id_usuario_pureza_radionuclidica_1){
                $pdf->SetXY(168, 140.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_pureza_radionuclidica_1)->username . " - " . $registro_lote->id_usuario_pureza_radionuclidica_1);
            }

            $pdf->SetXY(121.5, 160);
            $pdf->Write(10, $registro_lote->pureza_radionuclidica_2_resultado);

            $pdf->SetXY(144, 160);
            $pdf->Write(10, $registro_lote->pureza_radionuclidica_2_data);

            if ($registro_lote->id_usuario_pureza_radionuclidica_2){
                $pdf->SetXY(168, 160);
                $pdf->Write(10, User::find($registro_lote->id_usuario_pureza_radionuclidica_2)->username . " - " . $registro_lote->id_usuario_pureza_radionuclidica_2);
            }

            $pdf->SetXY(121.5, 180);
            $pdf->Write(10, $registro_lote->meia_vida_resultado);

            $pdf->SetXY(144, 180);
            $pdf->Write(10, $registro_lote->meia_vida_data);

            if ($registro_lote->id_usuario_meia_vida){
                $pdf->SetXY(168, 180);
                $pdf->Write(10, User::find($registro_lote->id_usuario_meia_vida)->username . " - " . $registro_lote->id_usuario_meia_vida);
            }

            $pdf->SetXY(121.5, 206);
            $pdf->Write(10, $registro_lote->solventes_resultado);

            $pdf->SetXY(144, 206);
            $pdf->Write(10, $registro_lote->solventes_data);

            if ($registro_lote->id_usuario_solventes){
                $pdf->SetXY(168, 206);
                $pdf->Write(10, User::find($registro_lote->id_usuario_solventes)->username . " - " . $registro_lote->id_usuario_solventes);
            }

            $pdf->SetFont('Arial', '', 12   );

            // ---------------------------------------------------//

            $pdf->SetXY(57, 239);

            $texto = utf8_decode($registro_lote->ocorrencias_p11);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;
            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            if ($registro_lote->id_usuario_verificacao_ocorrencias_p11){
                $pdf->SetXY(139, 255.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_ocorrencias_p11)->username . " - " . $registro_lote->id_usuario_verificacao_ocorrencias_p11);
            }

        // PAGINA 12

            $pdf->AddPage();
            $tplIdx = $pdf->importPage(12);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            if ($registro_lote->pureza_radioquimica_a_codigo === false){
                $pdf->SetXY(114.5, 87);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->pureza_radioquimica_a_codigo === true){
                $pdf->SetXY(114.5, 94.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(121.5, 92);
            $pdf->Write(10, $registro_lote->pureza_radioquimica_a_resultado);

            $pdf->SetXY(144, 92);
            $pdf->Write(10, $registro_lote->pureza_radioquimica_a_data);

            if ($registro_lote->id_usuario_pureza_radioquimica_a){
                $pdf->SetXY(165, 92);
                $pdf->Write(10, User::find($registro_lote->id_usuario_pureza_radioquimica_a)->username . " - " . $registro_lote->id_usuario_pureza_radioquimica_a);
            }

            $pdf->SetXY(121.5, 125);
            $pdf->Write(10, $registro_lote->pureza_radioquimica_b_resultado);

            $pdf->SetXY(144, 125);
            $pdf->Write(10, $registro_lote->pureza_radioquimica_b_data);
            
            if ($registro_lote->id_usuario_pureza_radioquimica_b){
                $pdf->SetXY(165, 125);
                $pdf->Write(10, User::find($registro_lote->id_usuario_pureza_radioquimica_b)->username . " - " . $registro_lote->id_usuario_pureza_radioquimica_b);
            }

            $pdf->SetXY(121.5, 156);
            $pdf->Write(10, $registro_lote->pureza_quimica_resultado);

            $pdf->SetXY(144, 156);
            $pdf->Write(10, $registro_lote->pureza_quimica_data);

            if ($registro_lote->id_usuario_pureza_quimica){
                $pdf->SetXY(165, 156);
                $pdf->Write(10, User::find($registro_lote->id_usuario_pureza_quimica)->username . " - " . $registro_lote->id_usuario_pureza_quimica);
            }

            $pdf->SetFont('Arial', '', 12);

            // ---------------------------------------------------//

            $pdf->SetXY(57, 192);

            $texto = utf8_decode($registro_lote->ocorrencias_p12);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;
            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            if ($registro_lote->id_usuario_verificacao_ocorrencias_p12){
                $pdf->SetXY(139, 209);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_ocorrencias_p12)->username . " - " . $registro_lote->id_usuario_verificacao_ocorrencias_p12);
            }

            // ---------------------------------------------------//

            if ($registro_lote->aprovacao_fisico_quimico === true){
                $pdf->SetXY(77, 237.5);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->aprovacao_fisico_quimico === false){
                $pdf->SetXY(157.5, 237.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(42, 248);
            $pdf->Write(10, $registro_lote->data_aprovacao_fisico_quimico);

            if ($registro_lote->id_usuario_aprovacao_fisico_quimico){
                $pdf->SetXY(121, 248);
                $pdf->Write(10, User::find($registro_lote->id_usuario_aprovacao_fisico_quimico)->username . " - " . $registro_lote->id_usuario_aprovacao_fisico_quimico);
            }
        
        // PÁGINA 13

            $pdf->AddPage();
            $tplIdx = $pdf->importPage(13);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            if ($registro_lote->endotoxinas_codigo === false){
                $pdf->SetXY(110, 106);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->endotoxinas_codigo === true){
                $pdf->SetXY(110, 110.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetFont('Arial', '', 10);
            if ($registro_lote->endotoxinas_1_resultado){
                $pdf->SetXY(130, 95);
                $pdf->Write(10, $registro_lote->endotoxinas_1_resultado .  " EU/ml");
            }

            if ($registro_lote->endotoxinas_2_resultado){
                $pdf->SetXY(130, 102);
                $pdf->Write(10, $registro_lote->endotoxinas_2_resultado . " %");
            }
            if ($registro_lote->endotoxinas_3_resultado){
                $pdf->SetXY(130, 109);
                $pdf->Write(10, $registro_lote->endotoxinas_3_resultado . " %");
            }
            if ($registro_lote->endotoxinas_4_resultado){   
                $pdf->SetXY(130, 116);
                $pdf->Write(10, $registro_lote->endotoxinas_4_resultado . " %");
            }

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(153, 105);
            $pdf->Write(10, $registro_lote->endotoxinas_data);

            if ($registro_lote->id_usuario_endotoxinas){
                $pdf->SetXY(171, 105);
                $pdf->Write(10, User::find($registro_lote->id_usuario_endotoxinas)->username . " - " . $registro_lote->id_usuario_endotoxinas);
            }
            $pdf->SetFont('Arial', '', 12);

            $pdf->SetXY(53, 127);
            $pdf->Write(10, $registro_lote->codigo_calibracao_pts);

            $pdf->SetXY(153.5, 127);
            $pdf->Write(10, $registro_lote->lote_cartucho_pts);

            // ---------------------------------------------------//

            $pdf->SetFont('Arial', '', 11);
            $pdf->SetXY(30, 170);
            $pdf->Write(10, $registro_lote->membrana_equipamento);

            $pdf->SetXY(61, 170);
            $pdf->Write(10, $registro_lote->membrana_lote);

            $pdf->SetXY(92.5, 170);
            $pdf->Write(10, $registro_lote->membrana_validade);

            if ($registro_lote->id_usuario_membrana){
                $pdf->SetXY(153, 170);
                $pdf->Write(10, User::find($registro_lote->id_usuario_membrana)->username . " - " . $registro_lote->id_usuario_membrana);
            }

             // ---------------------------------------------------//

            $pdf->SetXY(30, 199);
            $pdf->Write(10, $registro_lote->pressao_teste_bolha_fornecida);

            $pdf->SetXY(78, 199);
            $pdf->Write(10, $registro_lote->pressao_teste_bolha_obtida);

            if ($registro_lote->id_usuario_pressao_teste_bolha){
                $pdf->SetXY(123, 199);
                $pdf->Write(10, User::find($registro_lote->id_usuario_pressao_teste_bolha)->username . " - " . $registro_lote->id_usuario_pressao_teste_bolha);
            }

            $pdf->SetFont('Arial', '', 12);

            // ---------------------------------------------------//

            $pdf->SetXY(57, 223);

            $texto = utf8_decode($registro_lote->ocorrencias_p13);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;
            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            if ($registro_lote->id_usuario_verificacao_ocorrencias_p13){
                $pdf->SetXY(139, 241);
                $pdf->Write(10, User::find($registro_lote->id_usuario_verificacao_ocorrencias_p13)->username . " - " . $registro_lote->id_usuario_verificacao_ocorrencias_p13);
            }

            // ---------------------------------------------------//

            if ($registro_lote->aprovacao_microbiologico === true){
                $pdf->SetXY(77, 250.5);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->aprovacao_microbiologico === false){
                $pdf->SetXY(157.5, 250.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(42, 261);
            $pdf->Write(10, $registro_lote->data_aprovacao_microbiologico);

            if ($registro_lote->id_usuario_aprovacao_microbiologico){
                $pdf->SetXY(121, 261);
                $pdf->Write(10, User::find($registro_lote->id_usuario_aprovacao_microbiologico)->username . " - " . $registro_lote->id_usuario_aprovacao_microbiologico);
            }

        // PAGINA 14

            $pdf->AddPage();
            $tplIdx = $pdf->importPage(14);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            $pdf->SetXY(76, 75.5);
            $pdf->Write(10, $registro_lote->esterilidade_data_inicio_analise);

            if ($registro_lote->id_usuario_esterilidade){
                $pdf->SetXY(127, 75.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_esterilidade)->username . " - " . $registro_lote->id_usuario_esterilidade);
            }

            // ---------------------------------------------------//

            if ($registro_lote->esterilidade_codigo == 1){
                $pdf->SetXY(90, 124);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->esterilidade_codigo == 2){
                $pdf->SetXY(90, 129.5);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->esterilidade_codigo == 3){
                $pdf->SetXY(90, 135);
                $pdf->Write(10, "X");
            }

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(122, 112);
            $pdf->Write(10, $registro_lote->esterilidade_1_resultado);

            $pdf->SetXY(144, 112);
            $pdf->Write(10, $registro_lote->esterilidade_1_data);
            
            if ($registro_lote->id_usuario_esterilidade_1){
                $pdf->SetXY(164, 112);
                $pdf->Write(10, User::find($registro_lote->id_usuario_esterilidade_1)->username . " - " . $registro_lote->id_usuario_esterilidade_1);
            }
           
            $pdf->SetXY(122, 132);
            $pdf->Write(10, $registro_lote->esterilidade_2_resultado);

            $pdf->SetXY(144, 132);
            $pdf->Write(10, $registro_lote->esterilidade_2_data);
            
            if ($registro_lote->id_usuario_esterilidade_2){
                $pdf->SetXY(164, 132);
                $pdf->Write(10, User::find($registro_lote->id_usuario_esterilidade_2)->username . " - " . $registro_lote->id_usuario_esterilidade_2);
            }
           
            $pdf->SetXY(122, 152);
            $pdf->Write(10, $registro_lote->esterilidade_3_resultado);

            $pdf->SetXY(144, 152);
            $pdf->Write(10, $registro_lote->esterilidade_3_data);
            
            if ($registro_lote->id_usuario_esterilidade_3){
                $pdf->SetXY(164, 152);
                $pdf->Write(10, User::find($registro_lote->id_usuario_esterilidade_3)->username . " - " . $registro_lote->id_usuario_esterilidade_3);
            }

            $pdf->SetFont('Arial', '', 12);

            // ---------------------------------------------------//
           
            $pdf->SetXY(57, 177);

            $texto = utf8_decode($registro_lote->ocorrencias_p14);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";
            $fim_Linha1 = false;
            foreach ($palavras as $palavra) {
                if (!$fim_Linha1 && $pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else{
                    $fim_Linha1 = true;
                    $restante .= " " . $palavra;
                }
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(158, 7, trim($restante), 0, 'L');
            }

            // ---------------------------------------------------//

            if ($registro_lote->aprovacao_esterilidade === true){
                $pdf->SetXY(77, 212.5);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->aprovacao_esterilidade === false){
                $pdf->SetXY(157.5, 212.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(42, 222.5);
            $pdf->Write(10, $registro_lote->data_aprovacao_esterilidade);

            if ($registro_lote->id_usuario_aprovacao_esterilidade){
                $pdf->SetXY(121, 222.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_aprovacao_esterilidade)->username . " - " . $registro_lote->id_usuario_aprovacao_esterilidade);
            }

        // PAGINA 15

            $pdf->AddPage();
            $tplIdx = $pdf->importPage(15);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 50);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            if ($registro_lote->id_usuario_supervisor_controle_qualidade){
                $pdf->SetXY(51, 86);
                $pdf->Write(10, User::find($registro_lote->id_usuario_supervisor_controle_qualidade)->username . " - " . $registro_lote->id_usuario_supervisor_controle_qualidade);
            }

            if ($registro_lote->atendimento_criterios === true){
                $pdf->SetXY(30.5, 113.5);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->atendimento_criterios === false){
                $pdf->SetXY(46.5, 113.5);
                $pdf->Write(10, "X");
            }

            $pdf->SetXY(44, 130);
            $pdf->Write(10, $lote);

            if ($registro_lote->aprovacao_lote === true){
                $pdf->SetXY(89, 131.5);
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->aprovacao_lote === false){
                $pdf->SetXY(120.5, 131.5);
                $pdf->Write(10, "X");
            }

            if ($registro_lote->id_usuario_resposavel_garantia_qualidade){
                $pdf->SetXY(52, 160);
                $pdf->Write(10, User::find($registro_lote->id_usuario_resposavel_garantia_qualidade)->username . " - " . $registro_lote->id_usuario_resposavel_garantia_qualidade);
            }

            $pdf->SetXY(80, 173);
            $pdf->Write(10, substr($registro_lote->hora_emissao_laudo, 0, -3));

        $pdf->Output('registro_de_lote_preenchido.pdf', 'I');
    }
}
