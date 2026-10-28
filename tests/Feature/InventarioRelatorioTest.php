<?php

namespace Tests\Feature;

use App\Http\Controllers\RelatoriosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

// Isolate mPDF; use SQLite memory or explicitly configured PostgreSQL TEMP tables.
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class InventarioRelatorioTest extends TestCase
{
    use \Tests\Support\PostgresTemporario;
    use \Tests\Support\VerificaPdf;

    protected function setUp(): void
    {
        parent::setUp();
        if (getenv('SIAPRA_TEST_PGSQL') === '1') {
            $this->iniciarPostgresTemporario();
        } else {
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => ':memory:',
                'database.connections.sqlite.url' => null,
            ]);
            DB::purge('sqlite');
            $this->assertSame('sqlite', DB::connection()->getDriverName());
            $this->assertSame(':memory:', DB::connection()->getDatabaseName());
            DB::connection()->getPdo()->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
            $this->assertSame('', DB::selectOne('PRAGMA database_list')->file);
        }

        // Same isolated-fixture pattern as StatusLoteTest; no RefreshDatabase/migrations.
        foreach ([
            'status_produtos (id INTEGER PRIMARY KEY, status TEXT)',
            'tipos_produtos (id INTEGER PRIMARY KEY, nome TEXT)',
            'produtos (id INTEGER PRIMARY KEY, nome TEXT, id_tipo INTEGER)',
            'produtos_mov_in (id '.(getenv('SIAPRA_TEST_PGSQL') === '1' ? 'SERIAL' : 'INTEGER').' PRIMARY KEY, id_produto INTEGER, qtd_itens_recebidos INTEGER, qtd_itens_estoque INTEGER, preco NUMERIC(9,2), data_entrega DATE, status_lote INTEGER, data_validade DATE, quarentena TEXT)',
        ] as $table) {
            DB::statement('CREATE TEMPORARY TABLE '.$table);
        }
        DB::table('tipos_produtos')->insert(['id' => 1, 'nome' => 'Material']);
        DB::table('produtos')->insert(['id' => 205, 'nome' => 'Produto inventariado', 'id_tipo' => 1]);
        DB::table('status_produtos')->insert([
            ['id' => 41, 'status' => 'APROVADO'],
            ['id' => 77, 'status' => "\u{00A0}Áprovádo\u{00A0}"],
            ['id' => 90, 'status' => 'REPROVADO'],
            ['id' => 91, 'status' => 'DESCARTADO'],
        ]);
    }

    protected function tearDown(): void
    {
        $this->encerrarPostgresTemporario();
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function lote(array $data = []): void
    {
        DB::table('produtos_mov_in')->insert(array_replace([
            'id_produto' => 205, 'qtd_itens_recebidos' => 100, 'qtd_itens_estoque' => 100,
            'preco' => 500, 'data_entrega' => '2026-09-15', 'status_lote' => 41,
            'data_validade' => '2027-12-31', 'quarentena' => 'NAO',
        ], $data));
    }

    private function relatorio(string $mes = 'todos', string $ano = '2026'): string
    {
        $html = '';
        File::shouldReceive('ensureDirectoryExists')->once()->with(storage_path('framework/mpdf'));
        $pdf = Mockery::mock('overload:Mpdf\\Mpdf');
        $pdf->shouldReceive('__construct')->once()->with(['tempDir' => storage_path('framework/mpdf')]);
        $pdf->shouldReceive('WriteHTML')->once()->andReturnUsing(function (string $content) use (&$html) {
            $html = $content;
        });
        $pdf->shouldReceive('Output')->once()->with('relatorio_inventario_almoxarifado_'.$ano.'_.pdf', 'D');
        (new RelatoriosController())->generate(Request::create('/', 'POST', [
            'tipo_relatorio' => 'inventario', 'ano' => $ano, 'mes' => $mes,
        ]));
        return preg_replace('/>\s+</', '><', $html);
    }

    public function test_titulo_do_inventario_e_neutro(): void
    {
        $html = $this->relatorio();
        $this->assertStringContainsString('<h2>Inventário do Almoxarifado</h2>', $html);
        $this->assertStringNotContainsString('DIPRA', strtoupper($html));
        $this->assertStringNotContainsString('SEPRA', strtoupper($html));
        $this->assertStringContainsString('Referência: 2026', $html);
        $this->assertStringContainsString('Saldo atual dos lotes aprovados recebidos até 31/12/2026', $html);
        $this->assertStringContainsString('Data de Emissão: '.date('d/m/Y'), $html);
    }

    private function assertProduto(string $html, int $qtd, string $valor): void
    {
        $this->assertStringContainsString(
            '<td>205</td><td>Produto inventariado</td><td>'.$qtd.'</td><td>R$ '.$valor.'</td>', $html
        );
        $this->assertSame(1, substr_count($html, '<td>205</td>'));
    }

    public static function saldos(): array
    {
        return [
            'ano selecionado e valor integral' => [[], 100, '500,00'],
            'saldo parcial e valor proporcional' => [['qtd_itens_estoque' => 40], 40, '200,00'],
            'ano anterior produto 205' => [['data_entrega' => '2025-02-19', 'qtd_itens_recebidos' => 1200, 'qtd_itens_estoque' => 1200, 'preco' => 2700], 1200, '2.700,00'],
            'metade do lote produto 205' => [['qtd_itens_recebidos' => 1200, 'qtd_itens_estoque' => 600, 'preco' => 2700], 600, '1.350,00'],
            'normalizacao status' => [['status_lote' => 77], 100, '500,00'],
            'recebidos zero' => [['qtd_itens_recebidos' => 0, 'qtd_itens_estoque' => 40], 40, '0,00'],
            'divisao fracionaria' => [['qtd_itens_recebidos' => 8, 'qtd_itens_estoque' => 1, 'preco' => 10], 1, '1,25'],
            'ultimo dia do ano' => [['data_entrega' => '2026-12-31'], 100, '500,00'],
            'validade e quarentena nao filtram inventario' => [['data_validade' => '2020-01-01', 'quarentena' => 'SIM'], 100, '500,00'],
        ];
    }

    #[DataProvider('saldos')]
    public function test_saldo_e_valor_dos_lotes_elegiveis(array $lote, int $qtd, string $valor): void
    {
        $this->lote($lote);
        $this->assertProduto($this->relatorio(), $qtd, $valor);
    }

    public static function excluidos(): array
    {
        return [
            'ano posterior' => [['data_entrega' => '2027-01-01']],
            'saldo zero' => [['qtd_itens_estoque' => 0]],
            'saldo negativo' => [['qtd_itens_estoque' => -1]],
            'reprovado com saldo' => [['status_lote' => 90]],
            'descartado com saldo' => [['status_lote' => 91]],
            'status ausente' => [['status_lote' => null]],
        ];
    }

    #[DataProvider('excluidos')]
    public function test_produto_sem_lotes_elegiveis_nao_aparece(array $lote): void
    {
        $this->lote($lote);
        $html = $this->relatorio();
        $this->assertStringNotContainsString('<td>205</td>', $html);
        $this->assertStringContainsString('Nenhum produto encontrado.', $html);
    }

    public function test_multiplos_lotes_somam_apenas_saldos_e_valores_elegiveis(): void
    {
        $this->lote(['data_entrega' => '2025-02-19', 'qtd_itens_recebidos' => 1200, 'qtd_itens_estoque' => 1200, 'preco' => 2700]);
        $this->lote(['qtd_itens_estoque' => 40]);
        $this->lote(['status_lote' => 90]);
        $this->lote(['status_lote' => 91]);
        $this->lote(['qtd_itens_estoque' => 0]);
        $this->lote(['data_entrega' => '2027-01-01']);
        // NULL from a zero denominator must not erase valid lot values.
        $this->lote(['qtd_itens_recebidos' => 0, 'qtd_itens_estoque' => 3]);
        $this->assertProduto($this->relatorio(), 1243, '2.900,00');
    }

    public function test_filtro_mensal_preserva_entradas_anteriores_e_inclui_ultimo_dia(): void
    {
        $this->lote(['data_entrega' => '2025-02-19']);
        $this->lote(['data_entrega' => '2026-08-31']);
        $this->lote(['data_entrega' => '2026-09-30']);
        $this->lote(['data_entrega' => '2026-10-01']);
        $html = $this->relatorio('9');
        $this->assertProduto($html, 300, '1.500,00');
        $this->assertStringContainsString('Referência: setembro/2026', $html);
        $this->assertStringContainsString('Saldo atual dos lotes aprovados recebidos até 30/09/2026', $html);
    }

    public function test_fim_de_fevereiro_em_ano_bissexto(): void
    {
        $this->lote(['data_entrega' => '2024-02-29']);
        $this->lote(['data_entrega' => '2024-03-01']);
        $this->assertProduto($this->relatorio('2', '2024'), 100, '500,00');
    }

    public function test_renderiza_inventario_com_mpdf_real(): void
    {
        $this->lote();
        $this->verificarPdfReal($this->relatorio('9'), 'inventario');
    }

    public function test_zebrado_e_nome_extenso_no_pdf_real(): void
    {
        for ($i = 0; $i < 24; $i++) {
            $id = 300 + $i;
            DB::table('produtos')->insert(['id' => $id, 'id_tipo' => 1,
                'nome' => $i === 0 ? 'Curto' : ($i === 1 ? 'Nome com varias palavras' : str_repeat('ProdutoABC123', 20).$i)]);
            $this->lote(['id_produto' => $id]);
        }
        $html = $this->relatorio('9');
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//tbody/tr') as $indice => $linha) {
            foreach ($linha->getElementsByTagName('td') as $celula) {
                $this->assertSame($indice % 2 ? 'linha-par' : '', $celula->getAttribute('class'));
            }
        }
        $antes = $this->verificarPdfReal(str_replace(' overflow: wrap;', '', $html), 'f7-inventario-antes');
        $depois = $this->verificarPdfReal($html, 'f6-f7-inventario', 2);
        $this->assertGreaterThanOrEqual(8, $depois['menor_fonte']);
        $this->assertGreaterThan($antes['menor_fonte'], $depois['menor_fonte']);
        $this->assertStringContainsString('0.969 0.969 0.969 rg', base64_decode($depois['conteudo']));
    }
}
