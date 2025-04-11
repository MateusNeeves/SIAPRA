<?php

namespace App\Http\Controllers;

use App\Models\User;
use setasign\Fpdi\Fpdi;
use App\Models\Planejamento;
use Illuminate\Http\Request;
use App\Models\Registro_Lote;
use Illuminate\Support\Facades\Auth;

class RegistrosLoteController extends Controller
{
    public function index(){
        $datas = Registro_Lote::select('data_fabricacao')->distinct()->pluck('data_fabricacao');
        return view('registros_lote/visualizar', ['datas' => $datas]);
    }

    public function register(){
        $usuarios = User::all();
        return view('registros_lote/cadastrar', ['usuarios' => $usuarios]);
    }

    public function store(Request $request){
        // SALVANDO O REGISTRO DE LOTE
            $registroLote = new Registro_Lote();

            $registroLote->lote = $request->lote;
            $registroLote->data_fabricacao = $request->data_fabricacao;
        
        // PAGINA 3

            $registroLote->lote_agua_enriquecida = $request->lote_agua_enriquecida;
            $registroLote->id_usuario_lote_agua_enriquecida = $request->id_usuario_lote_agua_enriquecida;
            
            $registroLote->pressao_ar_comprimido = $request->pressao_ar_comprimido;
            $registroLote->pressao_H = $request->pressao_H;
            $registroLote->pressao_He_refrigeracao = $request->pressao_He_refrigeracao;
            $registroLote->pressao_He_analitico = $request->pressao_He_analitico;
            $registroLote->radiacao_ambiental_lab = $request->radiacao_ambiental_lab;
            $registroLote->id_usuario_verificacao_p3 = $request->id_usuario_verificacao_p3;

            $registroLote->hora_inicio_irradiacao_agua_enriquecida = $request->hora_inicio_irradiacao_agua_enriquecida;
            $registroLote->hora_final_irradiacao_agua_enriquecida = $request->hora_final_irradiacao_agua_enriquecida;
            $registroLote->ativ_teorica_F18 = $request->ativ_teorica_F18;
            $registroLote->id_usuario_irradiacao_agua_enriquecida = $request->id_usuario_irradiacao_agua_enriquecida;

            $registroLote->hora_inicio_transferir_F18_sintese = $request->hora_inicio_transferir_F18_sintese;
            $registroLote->hora_final_transferir_F18_sintese = $request->hora_final_transferir_F18_sintese;
            $registroLote->id_usuario_transferir_F18_sintese = $request->id_usuario_transferir_F18_sintese;
            
            $registroLote->ocorrencias_p3 = $request->ocorrencias_p3;
            $registroLote->ocorrencias_horario_p3 = $request->ocorrencias_horario_p3;
            $registroLote->id_usuario_ocorrencias_p3 = $request->id_usuario_ocorrencias_p3;

            $registroLote->logbook_anexado = $request->logbook_anexado;
            $registroLote->logbook_data = $request->logbook_data;
            $registroLote->logbook_time = $request->logbook_time;
            $registroLote->id_usuario_logbook = $request->id_usuario_logbook;
        
        // PAGINA 4
            
            $registroLote->modulo_sintese = $request->modulo_sintese;

            $registroLote->kryptofix222_lote = $request->kryptofix222_lote;
            $registroLote->kryptofix222_data_validade = $request->kryptofix222_data_validade;
            $registroLote->triflato_manose_lote = $request->triflato_manose_lote;
            $registroLote->triflato_manose_data_validade = $request->triflato_manose_data_validade;
            $registroLote->hidroxido_sodio_lote = $request->hidroxido_sodio_lote;
            $registroLote->hidroxido_sodio_data_validade = $request->hidroxido_sodio_data_validade;
            $registroLote->agua_injetaveis_lote = $request->agua_injetaveis_lote;
            $registroLote->agua_injetaveis_data_validade = $request->agua_injetaveis_data_validade;
            $registroLote->acetronitrila_anidra_lote = $request->acetronitrila_anidra_lote;
            $registroLote->acetronitrila_anidra_data_validade = $request->acetronitrila_anidra_data_validade;
            $registroLote->ifp_synthera_lote = $request->ifp_synthera_lote;
            $registroLote->ifp_synthera_data_validade = $request->ifp_synthera_data_validade;

            $registroLote->sep_pak_lote = $request->sep_pak_lote;
            $registroLote->sep_pak_data_validade = $request->sep_pak_data_validade;
            $registroLote->coluna_scx_lote = $request->coluna_scx_lote;
            $registroLote->coluna_scx_data_validade = $request->coluna_scx_data_validade;
            $registroLote->coluna_c18_lote = $request->coluna_c18_lote;
            $registroLote->coluna_c18_data_validade = $request->coluna_c18_data_validade;
            $registroLote->coluna_alumina_lote = $request->coluna_alumina_lote;
            $registroLote->coluna_alumina_data_validade = $request->coluna_alumina_data_validade;
            $registroLote->seringa_3ml_lote = $request->seringa_3ml_lote;
            $registroLote->seringa_3ml_data_validade = $request->seringa_3ml_data_validade;
            $registroLote->agulha_05x25_lote = $request->agulha_05x25_lote;
            $registroLote->agulha_05x25_data_validade = $request->agulha_05x25_data_validade;
            $registroLote->agua_injetavel_seringa_lote = $request->agua_injetavel_seringa_lote;
            $registroLote->agua_injetavel_seringa_data_validade = $request->agua_injetavel_seringa_data_validade;
            $registroLote->etanol_seringa_lote = $request->etanol_seringa_lote;
            $registroLote->etanol_seringa_data_validade = $request->etanol_seringa_data_validade;
            $registroLote->NaHCO3_seringa_lote = $request->NaHCO3_seringa_lote;
            $registroLote->NaHCO3_seringa_data_validade = $request->NaHCO3_seringa_data_validade;

            $registroLote->id_usuario_separado_registrado_p4 = $request->id_usuario_separado_registrado_p4;
            $registroLote->data_separado_registrado_p4 = $request->data_separado_registrado_p4;

            $registroLote->id_usuario_recebido_conferido_p4 = $request->id_usuario_recebido_conferido_p4;
            $registroLote->data_recebido_conferido_p4 = $request->data_recebido_conferido_p4;

        // PAGINA 5
            
            $registroLote->hora_inicio_montagem_kit_synthera = $request->hora_inicio_montagem_kit_synthera;
            $registroLote->hora_final_montagem_kit_synthera = $request->hora_final_montagem_kit_synthera;
            $registroLote->id_usuario_execucao_montagem_kit_synthera = $request->id_usuario_execucao_montagem_kit_synthera;
            $registroLote->id_usuario_verificacao_montagem_kit_synthera = $request->id_usuario_verificacao_montagem_kit_synthera;
            
            $registroLote->temperatura_lab_producao = $request->temperatura_lab_producao;
            $registroLote->umidade_lab_producao = $request->umidade_lab_producao;
            $registroLote->id_usuario_verificacao_p5 = $request->id_usuario_verificacao_p5;

            $registroLote->limpeza_celula = $request->has('limpeza_celula') ? true : false;
            $registroLote->verif_volume_H218O = $request->has('verif_volume_H218O') ? true : false;
            $registroLote->verif_frasco_rejeitos = $request->has('verif_frasco_rejeitos') ? true : false;
            $registroLote->verif_bolsa_ar = $request->has('verif_bolsa_ar') ? true : false;
            $registroLote->abrir_valvula_ar_comprimido = $request->has('abrir_valvula_ar_comprimido') ? true : false;
            $registroLote->abrir_valvula_nitrogenio = $request->has('abrir_valvula_nitrogenio') ? true : false;
            $registroLote->verif_pos_capilares = $request->has('verif_pos_capilares') ? true : false;
            $registroLote->ligar_controle_synthera = $request->has('ligar_controle_synthera') ? true : false;
            $registroLote->ligar_notebook_synthera = $request->has('ligar_notebook_synthera') ? true : false;
            $registroLote->iniciar_programa_mpb = $request->has('iniciar_programa_mpb') ? true : false;
            $registroLote->retirar_ifp_usado = $request->has('retirar_ifp_usado') ? true : false;
            $registroLote->inserir_ifp_synthera = $request->has('inserir_ifp_synthera') ? true : false;
            $registroLote->conectar_theodorico = $request->has('conectar_theodorico') ? true : false;

        // PAGINA 6

            $registroLote->iniciar_auto_teste = $request->has('iniciar_auto_teste') ? true : false;
            $registroLote->efetuar_diluicao_triflato_manose = $request->has('efetuar_diluicao_triflato_manose') ? true : false;
            $registroLote->remover_bloco_vermelho = $request->has('remover_bloco_vermelho') ? true : false;
            $registroLote->fechar_portas_bbs = $request->has('fechar_portas_bbs') ? true : false;
            $registroLote->pressionar_start = $request->has('pressionar_start') ? true : false;
            $registroLote->id_usuario_verificacao_acoes = $request->id_usuario_verificacao_acoes;

            $registroLote->ativ_chegada_18F = $request->ativ_chegada_18F;
            $registroLote->ativ_residual_18F = $request->ativ_residual_18F;
            $registroLote->ativ_modulo_sintese = $request->ativ_modulo_sintese;
            $registroLote->ativ_modulo_fracionamento = $request->ativ_modulo_fracionamento;
            $registroLote->hora_inicio_sintese = $request->hora_inicio_sintese;
            $registroLote->hora_final_sintese = $request->hora_final_sintese;
            $registroLote->rendimento_sintese = $request->rendimento_sintese;
            $registroLote->id_usuario_execucao_p6 = $request->id_usuario_execucao_p6;
            $registroLote->id_usuario_verificacao_p6 = $request->id_usuario_verificacao_p6;

            $registroLote->ocorrencias_p6 = $request->ocorrencias_p6;
            $registroLote->ocorrencias_horario_p6 = $request->ocorrencias_horario_p6;
            $registroLote->id_usuario_execucao_ocorrencias_p6 = $request->id_usuario_execucao_ocorrencias_p6;
            $registroLote->id_usuario_verificacao_ocorrencias_p6 = $request->id_usuario_verificacao_ocorrencias_p6;

        // PAGINA 7

            $registroLote->kit_fracionamento_1_lote = $request->kit_fracionamento_1_lote;
            $registroLote->kit_fracionamento_1_data_validade = $request->kit_fracionamento_1_data_validade;
            $registroLote->kit_fracionamento_2_lote = $request->kit_fracionamento_2_lote;
            $registroLote->kit_fracionamento_2_data_validade = $request->kit_fracionamento_2_data_validade;
            $registroLote->filtro_millex_gs_lote = $request->filtro_millex_gs_lote;
            $registroLote->filtro_millex_gs_data_validade = $request->filtro_millex_gs_data_validade;
            $registroLote->filtro_millex_gv_lote = $request->filtro_millex_gv_lote;
            $registroLote->filtro_millex_gv_data_validade = $request->filtro_millex_gv_data_validade;
            $registroLote->soro_fisiologico_lote = $request->soro_fisiologico_lote;
            $registroLote->soro_fisiologico_data_validade = $request->soro_fisiologico_data_validade;
            $registroLote->agulha_09x40_lote = $request->agulha_09x40_lote;
            $registroLote->agulha_09x40_data_validade = $request->agulha_09x40_data_validade;
            $registroLote->frascos_15ml_lote = $request->frascos_15ml_lote;
            $registroLote->frascos_15ml_qtd = $request->frascos_15ml_qtd;
            $registroLote->frascos_15ml_data_validade = $request->frascos_15ml_data_validade;
            $registroLote->frascos_bulk_lote = $request->frascos_bulk_lote;
            $registroLote->frascos_bulk_data_validade = $request->frascos_bulk_data_validade;
            $registroLote->id_usuario_separado_registrado_p7 = $request->id_usuario_separado_registrado_p7;
            $registroLote->data_separado_registrado_p7 = $request->data_separado_registrado_p7;
            $registroLote->id_usuario_recebido_conferido_p7 = $request->id_usuario_recebido_conferido_p7;
            $registroLote->data_recebido_conferido_p7 = $request->data_recebido_conferido_p7;

            $registroLote->ligar_theodorico = $request->has('ligar_theodorico') ? true : false;
            $registroLote->colocar_castelo_chumbo_dws = $request->has('colocar_castelo_chumbo_dws') ? true : false;
            $registroLote->pressionar_botao_park = $request->has('pressionar_botao_park') ? true : false;
            $registroLote->pressionar_botao_pinch_open = $request->has('pressionar_botao_pinch_open') ? true : false;
            $registroLote->retirar_kit_usado = $request->has('retirar_kit_usado') ? true : false;
            $registroLote->realizar_limpeza_theodorico = $request->has('realizar_limpeza_theodorico') ? true : false;
            $registroLote->conectar_capilares_synthera_bulk = $request->has('conectar_capilares_synthera_bulk') ? true : false;
            $registroLote->conectar_kit_fracionamento_1 = $request->has('conectar_kit_fracionamento_1') ? true : false;
            $registroLote->fechar_bomba_peristaltica = $request->has('fechar_bomba_peristaltica') ? true : false;
        
        // PAGINA 8

            $registroLote->pressionar_botao_pinch_close = $request->has('pressionar_botao_pinch_close') ? true : false;
            $registroLote->conectar_kit_fracionamento_2 = $request->has('conectar_kit_fracionamento_2') ? true : false;
            $registroLote->prender_capilares_parede_theodorico = $request->has('prender_capilares_parede_theodorico') ? true : false;
            $registroLote->conectar_filtros_millex_gs = $request->has('conectar_filtros_millex_gs') ? true : false;
            $registroLote->verificar_linhas_conectadas = $request->has('verificar_linhas_conectadas') ? true : false;
            $registroLote->verificar_conexoes_capilares = $request->has('verificar_conexoes_capilares') ? true : false;
            $registroLote->verificar_agulha_succao = $request->has('verificar_agulha_succao') ? true : false;
            $registroLote->fechar_porta = $request->has('fechar_porta') ? true : false;
            $registroLote->programar_fracionamento_software = $request->has('programar_fracionamento_software') ? true : false;
            $registroLote->imprimir_etiqueta_frascos = $request->has('imprimir_etiqueta_frascos') ? true : false;
            $registroLote->alimentar_antecamara_frascos = $request->has('alimentar_antecamara_frascos') ? true : false;
            $registroLote->marcar_posicao_frascos = $request->has('marcar_posicao_frascos') ? true : false;
            $registroLote->pressionar_botao_from_synt = $request->has('pressionar_botao_from_synt') ? true : false;
            $registroLote->pressionar_botao_bulk_dilution = $request->has('pressionar_botao_bulk_dilution') ? true : false;
            $registroLote->pressionar_botao_start = $request->has('pressionar_botao_start') ? true : false;
            $registroLote->id_usuario_verificado_p8 = $request->id_usuario_verificado_p8;
            
            $registroLote->atividade_fdg_18f = $request->atividade_fdg_18f;
            $registroLote->volume_soro_fisiologico = $request->volume_soro_fisiologico;
            $registroLote->imprimir_anexar_relatorio_producao = $request->has('imprimir_anexar_relatorio_producao') ? true : false;
            $registroLote->hora_inicio_p8 = $request->hora_inicio_p8;
            $registroLote->hora_final_p8 = $request->hora_final_p8;
            $registroLote->id_usuario_fracionamento_executado = $request->id_usuario_fracionamento_executado;
        
        // PAGINA 9

            $registroLote->ocorrencias_p9 = $request->ocorrencias_p9;
            $registroLote->ocorrencias_horario_p9 = $request->ocorrencias_horario_p9;
            $registroLote->id_usuario_execucao_ocorrencias_p9 = $request->id_usuario_execucao_ocorrencias_p9;
            $registroLote->id_usuario_verificacao_ocorrencias_p9 = $request->id_usuario_verificacao_ocorrencias_p9;


        $registroLote->save();

        return redirect()->route('registros_lote')->with('alert-success', 'Registro de lote salvo com sucesso.');
    }
    
    public function make_pdf(Request $request){
        $registro_lote = Registro_Lote::where('data_fabricacao', $request->data_fabricacao)->get()[0];

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
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

        // PÁGINA 2
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(2);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

        // PÁGINA 3
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(3);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
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

            if ($registro_lote->logbook_anexado != null && $registro_lote->logbook_anexado == true) {
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
            } else if ($registro_lote->logbook_anexado != null && $registro_lote->logbook_anexado == false) {
                $pdf->SetXY(151.5, 252.5);
                $pdf->Write(10, "X");
            }

        // PÁGINA 4
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(4);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

            if ($registro_lote->modulo_sintese != null && $registro_lote->modulo_sintese == 0){
                $pdf->SetXY(100.5, 77 );
                $pdf->Write(10, "X");
            }
            else if ($registro_lote->modulo_sintese != null && $registro_lote->modulo_sintese == 1){
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
            
            $pdf->SetXY(39, 51);
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
            
            $pdf->SetXY(39, 51);
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
            
            $pdf->SetXY(39, 51);
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
            
            $pdf->SetXY(39, 51);
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

            $pdf->SetXY(103.5, 202.5);
            $pdf->Write(10, substr($registro_lote->atividade_fdg_18f, 0, -3) . " mCi");

            $pdf->SetXY(103.5, 210);
            $pdf->Write(10, substr($registro_lote->volume_soro_fisiologico, 0, -3) . " ml");

            
            if ($registro_lote->imprimir_anexar_relatorio_producao){
                $pdf->SetXY(143, 217.5);
                $pdf->Write(10, "X");
            }
            
            $pdf->SetXY(30, 232);
            $pdf->Write(10, substr($registro_lote->hora_inicio_p8, 0, -3) . " h");
            
            $pdf->SetXY(65, 232);
            $pdf->Write(10, substr($registro_lote->hora_final_p8, 0, -3) . " h");
            
            if ($registro_lote->id_usuario_fracionamento_executado){
                $pdf->SetXY(104, 229.5);
                $pdf->Write(10, User::find($registro_lote->id_usuario_fracionamento_executado)->username . " - " . $registro_lote->id_usuario_fracionamento_executado);
            }

        // PÁGINA 9
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(9);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
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
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

        $pdf->Output('registro_de_lote_preenchido.pdf', 'I');
    }
}
