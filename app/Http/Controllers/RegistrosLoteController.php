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

    public function store(Request $request)
    {
        // SALVANDO O REGISTRO DE LOTE
        $registroLote = new Registro_Lote();

        $registroLote->lote = $request->lote;
        $registroLote->data_fabricacao = $request->data_fabricacao;
        
        $registroLote->lote_agua_enriquecida = $request->lote_agua_enriquecida;
        $registroLote->id_usuario_lote_agua_enriquecida = Auth::user()->id;
        
        $registroLote->pressao_ar_comprimido = $request->pressao_ar_comprimido;
        $registroLote->pressao_H = $request->pressao_H;
        $registroLote->pressao_He_refrigeracao = $request->pressao_He_refrigeracao;
        $registroLote->pressao_He_analitico = $request->pressao_He_analitico;
        $registroLote->radiacao_ambiental_lab = $request->radiacao_ambiental_lab;
        $registroLote->id_usuario_verificacao_p3 = Auth::user()->id;

        $registroLote->hora_inicio_irradiacao_agua_enriquecida = $request->hora_inicio_irradiacao_agua_enriquecida;
        $registroLote->hora_final_irradiacao_agua_enriquecida = $request->hora_final_irradiacao_agua_enriquecida;
        $registroLote->ativ_teorica_F18 = $request->ativ_teorica_F18;
        $registroLote->id_usuario_irradiacao_agua_enriquecida = Auth::user()->id;

        $registroLote->hora_inicio_transferir_F18_sintese = $request->hora_inicio_transferir_F18_sintese;
        $registroLote->hora_final_transferir_F18_sintese = $request->hora_final_transferir_F18_sintese;
        $registroLote->id_usuario_transferir_F18_sintese = Auth::user()->id;
        
        $registroLote->ocorrencias_p3 = $request->ocorrencias_p3;
        $registroLote->ocorrencias_horario_p3 = $request->ocorrencias_horario_p3;
        $registroLote->id_usuario_ocorrencias_p3 = Auth::user()->id;

        $registroLote->logbook_anexado = $request->logbook_anexado ?? 0;
        $registroLote->logbook_data = $request->logbook_data;
        $registroLote->logbook_time = $request->logbook_time;
        $registroLote->id_usuario_logbook = Auth::user()->id;

        $registroLote->save();

        return redirect()->route('registros_lote')->with('alert-success', 'Registro de lote salvo com sucesso.');
    }
    

    public function make_pdf(Request $request)
    {
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


        $pdf->Output('registro_de_lote_preenchido.pdf', 'I');
    }
}
