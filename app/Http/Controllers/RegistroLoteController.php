<?php

namespace App\Http\Controllers;

use setasign\Fpdi\Fpdi;
use Illuminate\Http\Request;

class RegistroLoteController extends Controller
{
    public function make_relatorio()
    {
        
        $pdf = new FPDI();
        
        $pdfPath = './Registro_de_Lote_Layout.pdf';
        $pageCount = $pdf->setSourceFile($pdfPath);

        // Definir fonte e cor
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 255);

        // PÁGINA 1
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, "12345");

            $pdf->SetXY(155, 51);
            $pdf->Write(10, "14   03  2025");

        // PÁGINA 2
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(2);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            
            $pdf->SetXY(39, 51);
            $pdf->Write(10, "12345");

            $pdf->SetXY(155, 51);
            $pdf->Write(10, "14   03  2025");

        $pdf->Output('registro_de_lote_preenchido.pdf', 'I'); // 'I' exibe no navegador, 'D' força download

    }
}
