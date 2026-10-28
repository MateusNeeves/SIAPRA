<?php

namespace App\Http\Controllers;

use App\Models\Tipo_Produto;
use Mpdf\Mpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RelatoriosController extends Controller
{
    public function index(){
        return view('relatorios/visualizar');
    }

    public function generate(Request $request){	
        if ($request->tipo_relatorio == 'itens_a_vencer'){
            $dados = $request->validate(['meses' => ['required', 'integer', 'min:1', 'max:12']]);
            $meses = (int) $dados['meses'];
            // Um único NOW() do PostgreSQL governa a seleção e as datas impressas,
            // inclusive no resultado vazio e nos limites de mês/fuso horário.
            $resultado = DB::select(
                "WITH limites AS (
                    SELECT NOW() AS inicio, NOW() + CAST(? AS integer) * INTERVAL '1 month' AS fim
                ), itens AS (SELECT P.NOME, L.ID, F.NOME as FABRICANTE, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE,
                CASE 
                    WHEN (
                        SELECT SUM(L2.QTD_ITENS_ESTOQUE)
                        FROM PRODUTOS_MOV_IN AS L2
                        WHERE ID_PRODUTO = P.ID 
                        AND EXISTS (SELECT 1 FROM STATUS_PRODUTOS S WHERE S.ID = L2.STATUS_LOTE
                            AND " . statusLoteSql() . " = 'APROVADO')
                        AND L2.DATA_VALIDADE > limites.fim
                    ) > P.QTD_MINIMA THEN 'False'
                    ELSE 'True'
                END AS FIM_DE_ESTOQUE
                FROM PRODUTOS_MOV_IN L 
                CROSS JOIN limites
                INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) 
                INNER JOIN PRODUTOS P ON (P.ID = L.ID_PRODUTO) 
                WHERE L.DATA_VALIDADE BETWEEN limites.inicio AND limites.fim
                )
                SELECT itens.*, TO_CHAR(limites.inicio, 'DD/MM/YYYY') AS data_inicial,
                    TO_CHAR(limites.fim, 'DD/MM/YYYY') AS data_limite
                FROM limites LEFT JOIN itens ON TRUE
                ORDER BY itens.data_validade ASC",
                [$meses]
            );
            $data_inicial_formatada = $resultado[0]->data_inicial;
            $data_final_formatada = $resultado[0]->data_limite;
            $itens_a_vencer = array_values(array_filter($resultado, fn ($item) => $item->id !== null));
            $temMarcados = collect($itens_a_vencer)->contains(fn ($item) => $item->fim_de_estoque === 'True');
            $legenda = $temMarcados
                ? '* Considerando apenas os vencimentos, o estoque aprovado restante após ' . $data_final_formatada . ' ficará igual ou abaixo da quantidade mínima.'
                : '';

            $tempDir = storage_path('framework/mpdf');
            File::ensureDirectoryExists($tempDir);
            $mpdf = new Mpdf(['tempDir' => $tempDir]);

            $html = $this->estilosPdf() . $this->cabecalhoPdf('Itens a Vencer', [
                'Lotes com validade até ' . $data_final_formatada . ' (próximos ' . $meses . ' meses)',
            ], $legenda) . '
                <table>
                    <thead>
                        <tr>
                            <th width="21%">Produto</th>
                            <th width="8%">ID Lote</th>
                            <th width="15%">Fabricante</th>
                            <th width="15%">Lote Fabricante</th>
                            <th width="17%">Quantidade em Estoque</th>
                            <th width="14%">Data de Validade</th>
                            <th width="10%">Status de Baixa</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($itens_a_vencer as $indice => $item) {
                $marcado = $item->fim_de_estoque == 'True';
                $classe = $marcado ? 'estoque-baixo' : ($indice % 2 ? 'linha-par' : '');
                
                $html .= '
                    <tr >
                        <td class="' . $classe . '">' . htmlspecialchars($item->nome) . ($marcado ? ' *' : '') . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->id) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->fabricante) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->lote_fabricante) . '</td>
                        <td class="' . $classe . '">' . htmlspecialchars($item->qtd_itens_estoque) . '</td>
                        <td class="' . $classe . '">' . date('d/m/Y', strtotime($item->data_validade)) . '</td>
                        <td class="' . $classe . '"></td>
                    </tr>';
            }

            if (empty($itens_a_vencer)) {
                $html .= '<tr><td colspan="7">Nenhum lote vence no período.</td></tr>';
            }
            $html .= '</tbody></table>';

            // Escreva o conteúdo HTML no PDF
            $mpdf->WriteHTML($html);

            // Envie o PDF para o navegador

            $nome_arquivo = 'relatorio_itens_a_vencer__' . $data_inicial_formatada . '--' . $data_final_formatada . '__.pdf';

            $mpdf->Output($nome_arquivo, 'D');
        }
        else if ($request->tipo_relatorio == 'inventario'){

            $mesTexto = '';

        if ($request->mes && $request->mes != 'todos') {
            $meses = [
                1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO',
                4 => 'ABRIL', 5 => 'MAIO', 6 => 'JUNHO',
                7 => 'JULHO', 8 => 'AGOSTO', 9 => 'SETEMBRO',
                10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
            ];

        $mesTexto = ' - ' . $meses[(int)$request->mes];
    }

            $tipos = Tipo_Produto::all()->pluck('nome', 'id')->toArray();
            
            foreach ($tipos as $id => $nome) {
                $tipos[$id] = [
                    'nome' => $nome,
                    'inventario' => []
                ];
            }
            $dataFim = "{$request->ano}-12-31";

            if ($request->mes && $request->mes != 'todos') {
                $mes = str_pad($request->mes, 2, '0', STR_PAD_LEFT);

                $dataFim = date("Y-m-t", strtotime("{$request->ano}-{$mes}-01"));
            }

            // Saldo atual dos lotes recebidos até o limite, sem reconstruir estoque histórico.
            $inventario = DB::select(
                "SELECT P.ID, P.NOME, P.ID_TIPO,
                        COALESCE(SUM(L.QTD_ITENS_ESTOQUE), 0) AS QTD,
                        COALESCE(SUM(1.0 * L.PRECO * L.QTD_ITENS_ESTOQUE / NULLIF(L.QTD_ITENS_RECEBIDOS, 0)), 0) AS VALOR_TOTAL
                FROM PRODUTOS P
                INNER JOIN PRODUTOS_MOV_IN L 
                    ON L.ID_PRODUTO = P.ID 
                INNER JOIN STATUS_PRODUTOS S ON S.ID = L.STATUS_LOTE
                WHERE L.DATA_ENTREGA <= ?
                    AND L.QTD_ITENS_ESTOQUE > 0
                    AND " . statusLoteSql() . " = 'APROVADO'
                GROUP BY P.ID, P.NOME, P.ID_TIPO", 
                [$dataFim]
            );

            foreach ($inventario as $produto) {
                if (isset($tipos[$produto->id_tipo])) {
                    $tipos[$produto->id_tipo]['inventario'][] = [
                        'id' => $produto->id,
                        'nome' => $produto->nome,
                        'qtd' => $produto->qtd,
                        'valor_total' => $produto->valor_total
                    ];
                }
            }

            $tempDir = storage_path('framework/mpdf');
            File::ensureDirectoryExists($tempDir);
            $mpdf = new Mpdf(['tempDir' => $tempDir]);

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
                    .break-page {
                        page-break-before: always;
                    }
                </style>
                <p class="data-emissao">Data de Emissão: ' . date('d/m/Y') . '</p>
                <br>
                <h2>RELATÓRIO DO INVENTÁRIO DO ALMOXARIFADO DA SEPRA DE '
                . htmlspecialchars($request->ano) . $mesTexto . 
                '</h2>
            ';

            // Adiciona uma tabela para cada tipo de produto
            $first = true; // Controla a quebra de página
            foreach ($tipos as $id_tipo => $tipo) {
                // Adiciona quebra de página apenas a partir do segundo tipo
                if (!$first) {
                    $html .= '<div class="break-page"></div>';
                }
                $first = false;

                $html .= '<h3>Tipo de Produto: ' . htmlspecialchars($tipo['nome']) . '</h3>';
                $html .= '
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome do Produto</th>
                                <th>Quantidade</th>
                                <th>Valor Total (R$)</th>
                            </tr>
                        </thead>
                        <tbody>';
                
                // Se houver produtos no inventário
                if (!empty($tipo['inventario'])) {
                    foreach ($tipo['inventario'] as $produto) {
                        $html .= '
                            <tr>
                                <td>' . htmlspecialchars($produto['id']) . '</td>
                                <td>' . htmlspecialchars($produto['nome']) . '</td>
                                <td>' . htmlspecialchars($produto['qtd']) . '</td>
                                <td>R$ ' . number_format($produto['valor_total'], 2, ',', '.') . '</td>
                            </tr>';
                    }
                } else {
                    // Caso não haja produtos nesse tipo
                    $html .= '
                        <tr>
                            <td colspan="4">Nenhum produto encontrado.</td>
                        </tr>';
                }

                $html .= '</tbody></table>';
            }

            // Escreva o conteúdo HTML no PDF
            $mpdf->WriteHTML($html);

            $nome_arquivo = 'relatorio_inventario_almoxarifado_sepra_' . $request->ano . '_.pdf';

            $mpdf->Output($nome_arquivo, 'D');
        }

        else {
            return redirect()->back()->with('alert-danger', 'Tipo de Relatório Inválido.')->withInput();     
        }
    }
    private function estilosPdf(): string
    {
        // overflow: wrap é específico do mPDF: quebra sequências longas sem inserir
        // caracteres no texto. Larguras explícitas preservam as colunas numéricas.
        return '<style>
            body { font-family: Arial, sans-serif; color: #333333; }
            h2 { text-align: center; color: #ca500d; }
            .contexto, .data-emissao { text-align: center; }
            .data-emissao { font-size: 11px; color: #666666; margin-bottom: 12px; }
            .legenda { font-size: 11px; color: #555555; margin-bottom: 12px; }
            table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; overflow: wrap; }
            th, td { border: 1px solid #dddddd; padding: 8px; text-align: center; }
            th { background-color: #ca500d; color: white; font-weight: bold; }
            td { color: #333333; background-color: #ffffff; }
            td.linha-par { background-color: #f7f7f7; }
            td.estoque-baixo { background-color: #fdecea; color: #8a1c1c; }
            .break-page { page-break-before: always; }
        </style>';
    }

    private function cabecalhoPdf(string $titulo, array $linhasContexto, string $legenda = ''): string
    {
        $html = '<h2>' . e($titulo) . '</h2>';
        foreach ($linhasContexto as $linha) {
            $html .= '<p class="contexto">' . e($linha) . '</p>';
        }
        if ($legenda !== '') {
            $html .= '<p class="legenda">' . e($legenda) . '</p>';
        }
        return $html . '<p class="data-emissao">Data de Emissão: ' . date('d/m/Y') . '</p>';
    }
}
