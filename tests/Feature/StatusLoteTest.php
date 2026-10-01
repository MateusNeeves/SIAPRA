<?php

namespace Tests\Feature;

use App\Http\Controllers\ProdutosController;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Concerns\ManagesLoops;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StatusLoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
        Carbon::setTestNow('2026-09-30 12:00:00');
        foreach ([
            'status_produtos (id INTEGER PRIMARY KEY, status TEXT)',
            'fabricantes (id INTEGER PRIMARY KEY, nome TEXT)',
            'fornecedores (id INTEGER PRIMARY KEY, nome TEXT)',
            'tipos_produtos (id INTEGER PRIMARY KEY, nome TEXT)',
            'unidade_medida (id INTEGER PRIMARY KEY, nome TEXT)',
            'produtos (id INTEGER PRIMARY KEY, nome TEXT, descricao TEXT, id_tipo INTEGER, id_unidade_medida INTEGER, qtd_aceitavel INTEGER, qtd_minima INTEGER, quarentena TEXT)',
            'produtos_fab (id_produto INTEGER, id_fabricante INTEGER)',
            'produtos_forn (id_produto INTEGER, id_fornecedor INTEGER)',
            'produtos_mov_in (id INTEGER PRIMARY KEY, id_produto INTEGER, id_fabricante INTEGER, id_fornecedor INTEGER, lote_fabricante TEXT, qtd_itens_recebidos INTEGER, qtd_itens_estoque INTEGER, preco NUMERIC, data_entrega TEXT, data_validade TEXT, quarentena TEXT, status_lote INTEGER, descricao_status_lote TEXT)',
            'acoes (id INTEGER PRIMARY KEY, descricao TEXT)',
            'dest_produtos (id INTEGER PRIMARY KEY, nome TEXT)',
            'produtos_mov_out (id INTEGER PRIMARY KEY, id_produtos_mov_in INTEGER, id_destino INTEGER, qtd_itens_movidos INTEGER, data_mov_out TEXT, motivo_saida INTEGER)',
            'logs (id INTEGER PRIMARY KEY, id_user INTEGER, id_acao INTEGER, tipo TEXT, descricao TEXT, data_hora TEXT)',
        ] as $table) {
            DB::statement('CREATE TABLE '.$table);
        }
        foreach (['fabricantes', 'fornecedores', 'tipos_produtos', 'unidade_medida'] as $table) {
            DB::table($table)->insert(['id' => 1, 'nome' => 'Teste']);
        }
        DB::table('produtos')->insert(['id' => 1, 'nome' => 'Produto', 'id_tipo' => 1, 'id_unidade_medida' => 1, 'quarentena' => ' Não ']);
        DB::table('acoes')->insert(['id' => 1, 'descricao' => 'Movimentação (Entrada) de Produto']);
        DB::table('acoes')->insert(['id' => 2, 'descricao' => 'Movimentação (Saída) de Produto']);
        DB::table('dest_produtos')->insert(['id' => 1, 'nome' => 'Saída']);
        Auth::shouldReceive('user')->andReturn((object) ['id' => 1]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public static function valores(): array
    {
        $cases = [];
        foreach (['APROVADO', 'REPROVADO', 'DESCARTADO'] as $status) {
            foreach ([$status, ucfirst(strtolower($status)), strtolower($status), ' '.$status.' ', str_replace('A', 'Á', $status)] as $valor) {
                $cases[] = [$valor, $status];
            }
        }
        return $cases;
    }

    private function request(array $data = []): Request
    {
        return Request::create('/', 'POST', array_replace([
            'id_view' => 1, 'status_lote' => 2, 'fabricante' => 'Teste', 'fornecedor' => 'Teste',
            'lote_fabricante' => 'Lote', 'qtd_itens_recebidos' => 10, 'preco' => 1,
            'data_entrega' => '2026-09-29', 'data_validade' => '2026-09-30',
        ], $data));
    }

    private function renderFragment(string $startMarker, string $endMarker, array $variables): string
    {
        $source = file_get_contents(resource_path('views/produtos/visualizar.blade.php'));
        $start = strpos($source, $startMarker);
        $end = strpos($source, $endMarker, $start) + strlen($endMarker);
        $compiled = (new BladeCompiler(new Filesystem(), sys_get_temp_dir()))->compileString(substr($source, $start, $end - $start));
        $__env = new class { use ManagesLoops; };
        extract($variables);
        ob_start();
        try {
            eval('?>'.$compiled);
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    #[DataProvider('valores')]
    public function test_entrada_descricao_e_formulario(string $valor, string $canonico): void
    {
        DB::table('status_produtos')->insert(['id' => 2, 'status' => $valor]);
        $this->assertSame($canonico, normalizarStatusLote($valor));
        $exige = $canonico !== 'APROVADO';
        $controller = new ProdutosController();
        if ($exige) {
            try {
                $controller->store_mov_in($this->request());
                $this->fail('Descrição deveria ser obrigatória');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('descricao_status_lote', $exception->errors());
            }
            $this->assertSame(0, DB::table('produtos_mov_in')->count());
        }
        $controller->store_mov_in($this->request($exige ? ['descricao_status_lote' => 'Motivo'] : []));
        $lote = DB::table('produtos_mov_in')->first();
        $this->assertNotNull($lote, session('alert-danger', 'Entrada não persistida'));
        $this->assertEquals($exige ? 0 : 10, $lote->qtd_itens_estoque);
        $this->assertSame($exige ? 'Motivo' : null, $lote->descricao_status_lote);
        $this->assertEquals(2, $lote->status_lote);
    }

    #[DataProvider('valores')]
    public function test_total_e_selecao_sql_com_dados_legados(string $valor, string $canonico): void
    {
        DB::table('status_produtos')->insert([['id' => 2, 'status' => $valor], ['id' => 77, 'status' => $valor]]);
        foreach (['NAO', 'Não', ' Nao ', 'não', 'SIM', ' Sim '] as $quarentena) {
            foreach (['2026-09-29', '2026-09-30', '2026-10-01'] as $validade) {
                DB::table('produtos_mov_in')->delete();
                foreach ([2, 77] as $id) {
                    DB::table('produtos_mov_in')->insert(['id' => $id, 'id_produto' => 1, 'id_fabricante' => 1,
                        'lote_fabricante' => 'Legado', 'qtd_itens_estoque' => 10, 'status_lote' => $id,
                        'data_validade' => $validade, 'quarentena' => $quarentena]);
                }
                $valido = $canonico === 'APROVADO' && normalizarQuarentena($quarentena) === 'NAO' && $validade >= '2026-09-30';
                $infos = \App\Http\Controllers\get_infos_view($this->request());
                $html = $this->renderFragment('<!-- Lista de QTD em Estoque -->', '</table>', ['lotesV' => $infos['lotesV']]);
                $this->assertStringContainsString('Total: '.($valido ? 20 : 0).'</th>', $html);
                session()->forget('lotesP');
                (new ProdutosController())->mov_out_select($this->request());
                $this->assertCount($valido ? 2 : 0, session('lotesP', []));
            }
        }
    }

    #[DataProvider('valores')]
    public function test_opcao_do_formulario_independe_do_id(string $valor, string $canonico): void
    {
        $html = $this->renderFragment("<select\n            id=\"status_lote\"", '</select>', [
            'status_lotes' => [['id' => 2, 'status' => $valor], ['id' => 77, 'status' => $valor]],
        ]);
        $this->assertSame(2, substr_count($html, 'data-exige-descricao="'.($canonico === 'APROVADO' ? '0' : '1').'"'));
    }

    public function test_saida_direta_rejeita_status_nao_aprovado(): void
    {
        foreach ([' reprovado ', ' Descartado '] as $valor) {
            DB::table('status_produtos')->updateOrInsert(['id' => 1], ['status' => $valor]);
            DB::table('produtos_mov_in')->updateOrInsert(['id' => 1], ['status_lote' => 1, 'qtd_itens_estoque' => 10, 'id_produto' => 1]);
            try {
                (new ProdutosController())->store_mov_out($this->request(['id_lote' => 1, 'qtd_itens_movidos' => 1]));
                $this->fail('Saída deveria ser rejeitada');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('id_lote', $exception->errors());
            }
            $this->assertEquals(10, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        }
    }

    public function test_saida_aprovada_preserva_quantidade_e_lote_zerado_nao_fica_disponivel(): void
    {
        DB::table('status_produtos')->insert(['id' => 2, 'status' => ' aprovado ']);
        DB::table('produtos_mov_in')->insert(['id' => 1, 'status_lote' => 2, 'qtd_itens_estoque' => 10,
            'id_produto' => 1, 'id_fabricante' => 1, 'data_validade' => '2026-10-01', 'quarentena' => 'NAO']);
        $controller = new ProdutosController();
        $controller->store_mov_out($this->request(['id_lote' => 1, 'destino' => 1,
            'qtd_itens_movidos' => 10, 'data_mov_out' => '2026-09-30']));
        $this->assertSame(1, DB::table('produtos_mov_out')->count());
        $this->assertEquals(0, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        $controller->mov_out_select($this->request());
        $this->assertSame([], session('lotesP', []));
    }
}
