<?php

namespace App\Http\Controllers;

use DateTime;


use Mpdf\Mpdf;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelatoriosController extends Controller
{
    public function index(){
        return view('relatorios/visualizar');
    }

    public function generate(Request $request){	
        if ($request->tipo_relatorio == 'itens_a_vencer'){
            $itens_a_vencer = DB::select(
                "SELECT P.NOME, L.ID, F.NOME as FABRICANTE, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE, 
                CASE 
                    WHEN (
                        SELECT SUM(L2.QTD_ITENS_ESTOQUE)
                        FROM PRODUTOS_MOV_IN AS L2
                        WHERE ID_PRODUTO = P.ID 
                        AND L2.DATA_VALIDADE > NOW() + INTERVAL '$request->meses MONTH'
                    ) > P.QTD_MINIMA THEN 'False'
                    ELSE 'True'
                END AS FIM_DE_ESTOQUE
                FROM PRODUTOS_MOV_IN L 
                INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) 
                INNER JOIN PRODUTOS P ON (P.ID = L.ID_PRODUTO) 
                WHERE L.DATA_VALIDADE BETWEEN NOW() AND NOW() + INTERVAL '$request->meses MONTH'
                ORDER BY L.DATA_VALIDADE ASC"
            );

            $mpdf = new Mpdf();

            // Comece a construir o HTML para o relatório
            $html = '
                <style>
                    h2 {
                        text-align: center;
                    }
                    h4 {
                        text-align: center;
                        color: #ff0000;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-family: Arial, sans-serif;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: center;
                    }
                    th {
                        background-color: #ca500d;
                        color: white;
                        font-weight: bold;
                    }
                    tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }
                    .estoque-baixo {
                        color: #ff0000 !important;
                    }
                    .data-emissao {
                        text-align: center;
                        font-size: 14px;
                        font-style: italic;
                        margin-bottom: 10px;
                    }
                </style>

                <p class="data-emissao">Data de Emissão: ' . date('d/m/Y') . '</p>
                <br>
                <h2>RELATÓRIO DE ITENS A VENCER NO ALMOXARIFADO DA DIPRA NOS PRÓXIMOS ' . htmlspecialchars($request->meses) . ' MESES</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>ID Lote</th>
                            <th>Fabricante</th>
                            <th>Lote Fabricante</th>
                            <th>Quantidade em Estoque</th>
                            <th>Data de Validade</th>
                            <th>Status de Baixa</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($itens_a_vencer as $item) {
                $classe = $item->fim_de_estoque == 'True' ? 'estoque-baixo' : ''; // Define a classe condicionalmente
                
                $html .= '
                    <tr >
                        <td class="' . $classe . '">' . htmlspecialchars($item->nome) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->id) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->fabricante) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->lote_fabricante) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->qtd_itens_estoque) . '</td>
                        <td class="' . $classe . '">' . date('d/m/Y', strtotime($item->data_validade)) . '</td>
                        <td></td>
                    </tr>';
            }

            $html .= '
                </tbody>
            </table>

            <h4>*ITENS QUE FICARÃO COM O ESTOQUE ABAIXO DO VALOR MÍNIMO APÓS ' . htmlspecialchars($request->meses) . ' MESES</h4>';

            

            // Escreva o conteúdo HTML no PDF
            $mpdf->WriteHTML($html);

            // Envie o PDF para o navegador

            $data_inicial = new DateTime();  // Data atual
            $data_final = new DateTime();    // Data futura
            $data_final->add(new DateInterval("P{$request->meses}M"));  // Adiciona os meses
            
            $data_inicial_formatada = $data_inicial->format('d/m/Y');
            $data_final_formatada = $data_final->format('d/m/Y');

            $nome_arquivo = 'relatorio_itens_a_vencer__' . $data_inicial_formatada . '--' . $data_final_formatada . '__.pdf';

            $mpdf->Output($nome_arquivo, 'D');
        }
        else if ($request->tipo_relatorio == 'inventario'){

        }

        else {
            return redirect()->back()->with('alert-danger', 'Tipo de Relatório Inválido.')->withInput();     
        }
    }
}
