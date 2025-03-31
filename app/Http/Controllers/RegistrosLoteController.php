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

            $registroLote->logbook_anexado = $request->logbook_anexado ?? 0;
            $registroLote->logbook_data = $request->logbook_data;
            $registroLote->logbook_time = $request->logbook_time;
            $registroLote->id_usuario_logbook = $request->id_usuario_logbook;
        
        // PAGINA 4
            
            $registroLote->modulo_sintese = $request->modulo_sintese != "0";

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
            $registroLote->id_usuario_verificacao_acoes = $request->has('id_usuario_verificacao_acoes') ? true : false;

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
            
            $pdf->SetXY(58, 101);
            $pdf->Write(10, User::find($registro_lote->id_usuario_lote_agua_enriquecida)->username . " - " . $registro_lote->id_usuario_lote_agua_enriquecida);

            // ---------------------------------------------------//
            
            $pdf->SetXY(150, 120);
            $pdf->Write(10, $registro_lote->pressao_ar_comprimido . " bar");

            $pdf->SetXY(150, 128);
            $pdf->Write(10, $registro_lote->pressao_H . " bar");

            $pdf->SetXY(150, 134);
            $pdf->Write(10, $registro_lote->pressao_He_refrigeracao . " bar");

            $pdf->SetXY(150, 140);
            $pdf->Write(10, $registro_lote->pressao_He_analitico . " bar");

            $pdf->SetXY(150, 147.5);
            $pdf->Write(10, $registro_lote->radiacao_ambiental_lab . " " . chr(181) . "Sv/h");

            $pdf->SetXY(58, 155.5);
            $pdf->Write(10, User::find($registro_lote->id_usuario_lote_agua_enriquecida)->username . " - " . $registro_lote->id_usuario_lote_agua_enriquecida);

            // ---------------------------------------------------//

            $pdf->SetXY(30, 181.5);
            $pdf->Write(10, substr($registro_lote->hora_inicio_irradiacao_agua_enriquecida, 0, -3));

            $pdf->SetXY(56, 181.5);
            $pdf->Write(10, substr($registro_lote->hora_final_irradiacao_agua_enriquecida, 0, -3));

            $pdf->SetXY(84, 181.5);
            $pdf->Write(10, substr($registro_lote->ativ_teorica_F18, 0, -3) . " mCi");

            $pdf->SetXY(127, 178);
            $pdf->Write(10, User::find($registro_lote->id_usuario_lote_agua_enriquecida)->username . " - " . $registro_lote->id_usuario_lote_agua_enriquecida);

            // ---------------------------------------------------//

            $pdf->SetXY(30, 205);
            $pdf->Write(10, substr($registro_lote->hora_inicio_transferir_F18_sintese, 0, -3));

            $pdf->SetXY(76.5, 205);
            $pdf->Write(10, substr($registro_lote->hora_final_transferir_F18_sintese, 0, -3));

            $pdf->SetXY(126, 203);
            $pdf->Write(10, User::find($registro_lote->id_usuario_lote_agua_enriquecida)->username . " - " . $registro_lote->id_usuario_lote_agua_enriquecida);

            // ---------------------------------------------------//

            $pdf->SetXY(56, 218);

            $texto = utf8_decode($registro_lote->ocorrencias_p3);

            $palavras = explode(" ", $texto);
            $linha1 = "";
            $restante = "";

            foreach ($palavras as $palavra) {
                if ($pdf->GetStringWidth($linha1 . " " . $palavra) < 130)
                    $linha1 .= " " . $palavra;
                else
                    $restante .= " " . $palavra;
            }

            $pdf->Write(6, trim($linha1));

            if (!empty($restante)) {
                $pdf->SetXY(30, $pdf->GetY() + 6);
                $pdf->MultiCell(160, 6, trim($restante), 0, 'L');
            }

            $pdf->SetXY(47, 241);
            $pdf->Write(10, substr($registro_lote->ocorrencias_horario_p3, 0, -3));

            $pdf->SetXY(110, 241);
            $pdf->Write(10, User::find($registro_lote->id_usuario_ocorrencias_p3)->username . " - " . $registro_lote->id_usuario_ocorrencias_p3);

            // ---------------------------------------------------//

            if ($registro_lote->logbook_anexado) {
                $pdf->SetXY(105, 252.5);
                $pdf->Write(10, "X");

                $pdf->SetXY(42, 259);
                $pdf->Write(10, date("m/d/Y", strtotime($registro_lote->logbook_data)));

                $pdf->SetXY(91, 259);
                $pdf->Write(10, substr($registro_lote->logbook_time, 0, -3));

                $pdf->SetXY(140, 259);
                $pdf->Write(10, User::find($registro_lote->id_usuario_logbook)->username . " - " . $registro_lote->id_usuario_logbook);
            } else {
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

            if (!$registro_lote->modulo_sintese) {
                $pdf->SetXY(100.5, 77 );
                $pdf->Write(10, "X");
            }
            else {
                $pdf->SetXY(100.5, 84);
                $pdf->Write(10, "X");
            }

            // ---------------------------------------------------//

            $pdf->SetXY(111.5, 117);
            $pdf->Write(10, $registro_lote->kryptofix222_lote);

            $pdf->SetXY(168, 117);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->kryptofix222_data_validade)));
            
            $pdf->SetXY(111.5, 124.5);
            $pdf->Write(10, $registro_lote->triflato_manose_lote);
            
            $pdf->SetXY(168, 124.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->triflato_manose_data_validade)));
            
            $pdf->SetXY(111.5, 132);
            $pdf->Write(10, $registro_lote->hidroxido_sodio_lote);
            
            $pdf->SetXY(168, 132);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->hidroxido_sodio_data_validade)));
            
            $pdf->SetXY(111.5, 139);
            $pdf->Write(10, $registro_lote->agua_injetaveis_lote);
            
            $pdf->SetXY(168, 139);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agua_injetaveis_data_validade)));
            
            $pdf->SetXY(111.5, 146);
            $pdf->Write(10, $registro_lote->acetronitrila_anidra_lote);
            
            $pdf->SetXY(168, 146);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->acetronitrila_anidra_data_validade)));
            
            $pdf->SetXY(111.5, 153);
            $pdf->Write(10, $registro_lote->ifp_synthera_lote);
            
            $pdf->SetXY(168, 153);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->ifp_synthera_data_validade)));

            // ---------------------------------------------------//

            $pdf->SetXY(111.5, 176);
            $pdf->Write(10, $registro_lote->sep_pak_lote);
            
            $pdf->SetXY(168, 176);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->sep_pak_data_validade)));

            $pdf->SetXY(111.5, 183.5);
            $pdf->Write(10, $registro_lote->coluna_scx_lote);
            
            $pdf->SetXY(168, 183.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->coluna_scx_data_validade)));

            $pdf->SetXY(111.5, 190.5);
            $pdf->Write(10, $registro_lote->coluna_c18_lote);
            
            $pdf->SetXY(168, 190.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->coluna_c18_data_validade)));

            $pdf->SetXY(111.5, 197.5);
            $pdf->Write(10, $registro_lote->coluna_alumina_lote);
            
            $pdf->SetXY(168, 197.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->coluna_alumina_data_validade)));

            $pdf->SetXY(111.5, 205);
            $pdf->Write(10, $registro_lote->seringa_3ml_lote);
            
            $pdf->SetXY(168, 205);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->seringa_3ml_data_validade)));

            $pdf->SetXY(111.5, 212);
            $pdf->Write(10, $registro_lote->agulha_05x25_lote);
            
            $pdf->SetXY(168, 212);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agulha_05x25_data_validade)));

            $pdf->SetXY(111.5, 219.5);
            $pdf->Write(10, $registro_lote->agua_injetavel_seringa_lote);
            
            $pdf->SetXY(168, 219.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->agua_injetavel_seringa_data_validade)));

            $pdf->SetXY(111.5, 226.5);
            $pdf->Write(10, $registro_lote->etanol_seringa_lote);
            
            $pdf->SetXY(168, 226.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->etanol_seringa_data_validade)));

            $pdf->SetXY(111.5, 233.5);
            $pdf->Write(10, $registro_lote->NaHCO3_seringa_lote);
            
            $pdf->SetXY(168, 233.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->NaHCO3_seringa_data_validade)));

            // ---------------------------------------------------//

            $pdf->SetXY(82, 241);
            $pdf->Write(10, User::find($registro_lote->id_usuario_separado_registrado_p4)->username . " - " . $registro_lote->id_usuario_separado_registrado_p4);

            $pdf->SetXY(165, 241);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->data_separado_registrado_p4)));

            $pdf->SetXY(82, 248.5);
            $pdf->Write(10, User::find($registro_lote->id_usuario_recebido_conferido_p4)->username . " - " . $registro_lote->id_usuario_recebido_conferido_p4);

            $pdf->SetXY(165, 248.5);
            $pdf->Write(10, date('d/m/Y', strtotime($registro_lote->data_recebido_conferido_p4)));

        // PÁGINA 5
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(5);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, $lote);

            $pdf->SetXY(155, 51);
            $pdf->Write(10, $dia . "   " . $mes . "  " . $ano);

            // ---------------------------------------------------//

        $pdf->Output('registro_de_lote_preenchido.pdf', 'I');
    }
}
