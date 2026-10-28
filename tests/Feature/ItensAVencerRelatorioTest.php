<?php

namespace Tests\Feature;

use App\Http\Controllers\RelatoriosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class ItensAVencerRelatorioTest extends TestCase
{
    use \Tests\Support\PostgresTemporario;
    use \Tests\Support\VerificaPdf;

    protected function tearDown(): void
    {
        $this->encerrarPostgresTemporario();
        parent::tearDown();
    }

    private function fixtures(): void
    {
        $this->iniciarPostgresTemporario();
        foreach ([
            'produtos (id INTEGER PRIMARY KEY, nome TEXT, qtd_minima NUMERIC(10,3))',
            'fabricantes (id INTEGER PRIMARY KEY, nome TEXT)',
            'status_produtos (id INTEGER PRIMARY KEY, status TEXT)',
            'produtos_mov_in (id SERIAL PRIMARY KEY, id_produto INTEGER, id_fabricante INTEGER, lote_fabricante TEXT, qtd_itens_estoque INTEGER, data_validade DATE, status_lote INTEGER, quarentena TEXT)',
        ] as $table) {
            DB::statement('CREATE TEMPORARY TABLE '.$table);
        }
        DB::table('fabricantes')->insert(['id' => 1, 'nome' => 'Fabricante']);
        DB::table('status_produtos')->insert([
            ['id' => 41, 'status' => 'Aprovado'], ['id' => 90, 'status' => 'Reprovado'],
        ]);
        DB::table('produtos')->insert(['id' => 1, 'nome' => 'Produto <teste>', 'qtd_minima' => '10.000']);
    }

    private function lote(array $dados = []): void
    {
        DB::table('produtos_mov_in')->insert(array_replace([
            'id_produto' => 1, 'id_fabricante' => 1, 'lote_fabricante' => 'Lote',
            'qtd_itens_estoque' => 5, 'status_lote' => 41, 'quarentena' => 'NAO',
            'data_validade' => DB::raw("CURRENT_DATE + 1"),
        ], $dados));
    }

    private function relatorio(int $meses = 1): string
    {
        $html = '';
        File::shouldReceive('ensureDirectoryExists')->once();
        $pdf = Mockery::mock('overload:Mpdf\\Mpdf');
        $pdf->shouldReceive('__construct')->once();
        $pdf->shouldReceive('WriteHTML')->once()->andReturnUsing(function ($conteudo) use (&$html) {
            $html = $conteudo;
        });
        $datas = DB::selectOne("SELECT TO_CHAR(NOW(), 'DD/MM/YYYY') AS inicio,
            TO_CHAR(NOW() + CAST(? AS integer) * INTERVAL '1 month', 'DD/MM/YYYY') AS fim", [$meses]);
        $pdf->shouldReceive('Output')->once()->with('relatorio_itens_a_vencer__'.$datas->inicio.'--'.$datas->fim.'__.pdf', 'D');
        (new RelatoriosController())->generate(Request::create('/', 'POST', ['tipo_relatorio' => 'itens_a_vencer', 'meses' => $meses]));
        $this->assertStringContainsString('<h2>Itens a Vencer</h2>', $html);
        $this->assertStringNotContainsString('SEPRA', $html);
        $this->assertStringNotContainsString('DIPRA', $html);
        $this->assertStringContainsString('Lotes com validade até '.$datas->fim.' (próximos '.$meses.' meses)', $html);
        $this->assertStringContainsString('>Status de Baixa</th>', $html);
        return $html;
    }

    public static function saldos(): array
    {
        return [[11, 41, false], [10, 41, true], [9, 41, true], [null, 41, true], [100, 90, true]];
    }

    #[DataProvider('saldos')]
    public function test_marcacao_por_produto_e_legenda_condicional(?int $saldo, int $status, bool $marcado): void
    {
        $this->fixtures();
        $this->lote();
        $this->lote(['lote_fabricante' => 'Segundo lote']);
        if ($saldo !== null) {
            $this->lote(['qtd_itens_estoque' => $saldo, 'status_lote' => $status,
                'data_validade' => DB::raw("CURRENT_DATE + INTERVAL '3 months'")]);
        }
        $html = $this->relatorio();
        $this->assertSame($marcado ? 2 : 0, substr_count($html, 'Produto &lt;teste&gt; *'));
        $this->assertSame($marcado, str_contains($html, '<p class="legenda">'));
        if ($marcado) {
            $this->assertStringContainsString('ficará igual ou abaixo da quantidade mínima.', $html);
            $this->assertLessThan(strpos($html, '<table>'), strpos($html, '<p class="legenda">'));
        }
        preg_match_all('/<tbody>(.*?)<\/tbody>/s', $html, $tabelas);
        preg_match_all('/<tr\s*>(.*?)<\/tr>/s', $tabelas[1][0], $linhas);
        $this->assertCount(2, $linhas[1]);
        foreach ($linhas[1] as $linha) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $linha, $celulas);
            $this->assertCount(7, $celulas[1]);
            $this->assertSame('', $celulas[1][6]);
        }
    }

    public function test_mistura_produtos_e_preserva_selecao_saldo_zero_reprovado_quarentena(): void
    {
        $this->fixtures();
        $this->lote(['qtd_itens_estoque' => 0, 'status_lote' => 90, 'quarentena' => 'SIM']);
        DB::table('produtos')->insert(['id' => 2, 'nome' => 'Normal', 'qtd_minima' => 0]);
        $this->lote(['id_produto' => 2]);
        $this->lote(['id_produto' => 2, 'data_validade' => DB::raw("CURRENT_DATE + INTERVAL '3 months'")]);
        $this->lote(['lote_fabricante' => 'Vencido', 'data_validade' => DB::raw('CURRENT_DATE - 1')]);
        $html = $this->relatorio();
        $this->assertStringContainsString('Produto &lt;teste&gt; *', $html);
        $this->assertStringContainsString('>Normal</td>', $html);
        $this->assertStringNotContainsString('Normal *', $html);
        $this->assertStringNotContainsString('Vencido', $html);
    }

    public function test_relatorio_vazio_preserva_contexto_sem_legenda(): void
    {
        $this->fixtures();
        $html = $this->relatorio(12);
        $this->assertStringContainsString('Nenhum lote vence no período.', $html);
        $this->assertStringNotContainsString('<p class="legenda">', $html);
    }

    public function test_limites_e_soma_de_lotes_aprovados_preservam_regra_original(): void
    {
        $this->fixtures();
        // DATE de hoje fica antes de NOW() depois da meia-noite, como na consulta original.
        $this->lote(['lote_fabricante' => 'Hoje', 'data_validade' => DB::raw('CURRENT_DATE')]);
        $this->lote(['lote_fabricante' => 'No limite', 'data_validade' => DB::raw("(NOW() + INTERVAL '1 month')::date")]);
        $this->lote(['qtd_itens_estoque' => 6, 'data_validade' => DB::raw("CURRENT_DATE + INTERVAL '3 months'")]);
        $this->lote(['qtd_itens_estoque' => 5, 'data_validade' => DB::raw("CURRENT_DATE + INTERVAL '3 months'")]);
        $html = $this->relatorio();
        $this->assertStringContainsString('No limite', $html);
        $this->assertStringNotContainsString('>Hoje</td>', $html);
        $this->assertStringNotContainsString('Produto &lt;teste&gt; *', $html);
        $this->assertStringNotContainsString('<p class="legenda">', $html);
    }

    public static function invalidos(): array
    {
        return [[null], [''], [0], [-1], [13], ['1.5'], ['abc'], ["1' OR 1=1 --"], [['1']]];
    }

    public function test_zebrado_e_textos_longos_no_pdf_real(): void
    {
        $this->fixtures();
        DB::table('produtos')->where('id', 1)->update(['nome' => str_repeat('ProdutoABC123', 20)]);
        DB::table('produtos')->insert(['id' => 2, 'nome' => 'Nome com varias palavras para leitura', 'qtd_minima' => 0]);
        DB::table('fabricantes')->where('id', 1)->update(['nome' => str_repeat('FabricanteXYZ', 15)]);
        $this->lote(['id_produto' => 2, 'data_validade' => DB::raw("CURRENT_DATE + INTERVAL '3 months'")]);
        for ($i = 0; $i < 16; $i++) {
            $this->lote(['id_produto' => $i % 4 < 2 ? 2 : 1,
                'lote_fabricante' => str_repeat('AB12CD34EF56', 15).$i,
                'data_validade' => DB::raw('CURRENT_DATE + '.($i + 1))]);
        }
        $html = $this->relatorio();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        foreach (['', 'linha-par', 'estoque-baixo', 'estoque-baixo'] as $i => $classe) {
            $celulas = $xpath->query('//tbody/tr['.($i + 1).']/td');
            $this->assertCount(7, $celulas);
            foreach ($celulas as $celula) {
                $this->assertSame($classe, $celula->getAttribute('class'));
            }
        }
        $antes = $this->verificarPdfReal(str_replace(' overflow: wrap;', '', $html), 'f7-itens-antes');
        $depois = $this->verificarPdfReal($html, 'f6-f7-itens', 2);
        $this->assertGreaterThanOrEqual(8, $depois['menor_fonte']);
        $this->assertGreaterThan($antes['menor_fonte'], $depois['menor_fonte']);
        // Operadores de preenchimento efetivamente emitidos pelo mPDF, não apenas CSS.
        $conteudo = base64_decode($depois['conteudo']);
        $this->assertStringContainsString('0.969 0.969 0.969 rg', $conteudo);
        $this->assertStringContainsString('0.992 0.925 0.918 rg', $conteudo);
    }

    public static function tamanhosPdf(): array
    {
        return [[0, 1], [1, 1], [120, 2]];
    }

    #[DataProvider('tamanhosPdf')]
    public function test_mpdf_real_vazio_uma_linha_e_multiplas_paginas(int $linhas, int $paginas): void
    {
        $this->fixtures();
        for ($i = 0; $i < $linhas; $i++) {
            $this->lote(['lote_fabricante' => 'Lote '.$i]);
        }
        $html = $this->relatorio();
        $this->assertStringContainsString('<thead>', $html);
        $this->assertStringContainsString('td.estoque-baixo { background-color: #fdecea; color: #8a1c1c; }', $html);
        $this->verificarPdfReal($html, 'itens-'.$linhas, $paginas);
    }

    #[DataProvider('invalidos')]
    public function test_meses_invalidos_rejeitados_antes_de_consultar_banco($meses): void
    {
        DB::shouldReceive('select')->never();
        $this->expectException(ValidationException::class);
        (new RelatoriosController())->generate(Request::create('/', 'POST', ['tipo_relatorio' => 'itens_a_vencer', 'meses' => $meses]));
    }

    public function test_limite_postgresql_equivale_ao_intervalo_original_em_fim_de_mes_e_fusos(): void
    {
        $this->fixtures();
        foreach (['UTC', 'America/Bahia'] as $fuso) {
            DB::select("SELECT set_config('TimeZone', ?, true)", [$fuso]);
            $limite = DB::selectOne("SELECT TO_CHAR(TIMESTAMPTZ '2026-08-31 23:59:59' + CAST(? AS integer) * INTERVAL '1 month', 'DD/MM/YYYY') AS data,
                (TIMESTAMPTZ '2026-08-31 23:59:59' + CAST(? AS integer) * INTERVAL '1 month') =
                (TIMESTAMPTZ '2026-08-31 23:59:59' + INTERVAL '1 MONTH') AS equivalente", [1, 1]);
            $this->assertSame('30/09/2026', $limite->data);
            $this->assertTrue($limite->equivalente);
        }
    }
}
