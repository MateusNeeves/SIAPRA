<?php

namespace Tests\Feature;

use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\QuarentenaController;
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
                $valido = $canonico === 'APROVADO' && $validade >= '2026-09-30';
                $disponivel = $valido && normalizarQuarentena($quarentena) === 'NAO';
                $infos = \App\Http\Controllers\get_infos_view($this->request());
                $html = $this->renderFragment('<!-- Lista de QTD em Estoque -->', '</table>', ['lotesV' => $infos['lotesV']]);
                $this->assertStringContainsString('Total: '.($valido ? 20 : 0).'</th>', $html);
                session()->forget('lotesP');
                (new ProdutosController())->mov_out_select($this->request());
                $this->assertCount($disponivel ? 2 : 0, session('lotesP', []));
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

    public static function statusQuarentena(): array
    {
        return [
            [1, 'QUARENTENA'], [77, 'Quarentena'], [123, ' quarentena '],
            [89, "\tQuárentêna\n"], [56, "\u{00A0}QUARENTENA\u{00A0}"],
        ];
    }

    #[DataProvider('statusQuarentena')]
    public function test_entrada_nao_oferece_status_quarentena(int $id, string $nome): void
    {
        DB::table('status_produtos')->insert([
            ['id' => $id, 'status' => $nome],
            ['id' => 2, 'status' => 'APROVADO'],
            ['id' => 3, 'status' => 'REPROVADO'],
            ['id' => 4, 'status' => 'DESCARTADO'],
            ['id' => 5, 'status' => 'OUTRO'],
        ]);
        (new ProdutosController())->mov_in($this->request());
        $opcoes = session('status_lotes');
        $this->assertEqualsCanonicalizing(['APROVADO', 'REPROVADO', 'DESCARTADO', 'OUTRO'], array_column($opcoes, 'status'));
        $html = $this->renderFragment("<select\n            id=\"status_lote\"", '</select>', ['status_lotes' => $opcoes]);
        $this->assertStringNotContainsString('value="'.$id.'"', $html);
        foreach ($opcoes as $opcao) {
            $this->assertStringContainsString('value="'.$opcao['id'].'"', $html);
        }
        $this->assertSame($nome, DB::table('status_produtos')->where('id', $id)->value('status'));
    }

    #[DataProvider('statusQuarentena')]
    public function test_entrada_direta_rejeita_status_quarentena(int $id, string $nome): void
    {
        DB::table('status_produtos')->insert(['id' => $id, 'status' => $nome]);
        try {
            (new ProdutosController())->store_mov_in($this->request([
                'status_lote' => $id, 'descricao_status_lote' => 'Tentativa direta',
            ]));
            $this->fail('Status QUARENTENA deveria ser rejeitado.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('status_lote', $exception->errors());
        }
        $this->assertSame(0, DB::table('produtos_mov_in')->count());
        $this->assertSame(0, DB::table('logs')->count());
        $this->assertSame($nome, DB::table('status_produtos')->where('id', $id)->value('status'));
    }

    public static function politicaQuarentena(): array
    {
        return [
            'produto SIM sem parametro' => ['SIM', []],
            'produto NAO sem parametro' => ['NAO', []],
            'tentativa SIM para NAO' => ['SIM', ['quarentena' => 'NAO']],
            'tentativa NAO para SIM' => ['NAO', ['quarentena' => 'SIM']],
        ];
    }

    #[DataProvider('politicaQuarentena')]
    public function test_entrada_deriva_quarentena_exclusivamente_do_produto(string $politica, array $parametros): void
    {
        DB::table('produtos')->where('id', 1)->update(['quarentena' => $politica]);
        DB::table('status_produtos')->insert(['id' => 2, 'status' => 'APROVADO']);
        (new ProdutosController())->store_mov_in($this->request($parametros));
        $lote = DB::table('produtos_mov_in')->first();
        $this->assertNotNull($lote, session('alert-danger', 'Entrada não persistida'));
        $this->assertSame($politica, $lote->quarentena);
        $this->assertEquals(10, $lote->qtd_itens_estoque);
        $this->assertSame($politica, DB::table('produtos')->value('quarentena'));
        $this->assertCount($politica === 'SIM' ? 1 : 0, (new QuarentenaController())->index()->getData()['lotes']);
    }

    public function test_retirada_preserva_politica_do_produto_e_novas_entradas(): void
    {
        DB::table('produtos')->where('id', 1)->update(['quarentena' => 'SIM']);
        DB::table('status_produtos')->insert(['id' => 2, 'status' => 'APROVADO']);
        DB::table('acoes')->insert(['id' => 3, 'descricao' => 'Retirada de Lote da Quarentena']);
        $controller = new ProdutosController();
        $controller->store_mov_in($this->request());
        $lote = DB::table('produtos_mov_in')->first();
        $this->assertNotNull($lote);
        $this->assertSame('SIM', $lote->quarentena);
        $quarentena = new QuarentenaController();
        $this->assertCount(1, $quarentena->index()->getData()['lotes']);
        $quarentena->remove($this->request(['id_lote' => $lote->id]));
        $this->assertSame('NAO', DB::table('produtos_mov_in')->where('id', $lote->id)->value('quarentena'));
        $this->assertSame('SIM', DB::table('produtos')->value('quarentena'));
        $this->assertCount(0, $quarentena->index()->getData()['lotes']);
        $controller->store_mov_in($this->request());
        $this->assertSame(['NAO', 'SIM'], DB::table('produtos_mov_in')->orderBy('id')->pluck('quarentena')->all());
        $this->assertCount(1, $quarentena->index()->getData()['lotes']);
    }

    #[DataProvider('politicaQuarentena')]
    public function test_edicao_do_produto_nao_altera_quarentena_do_lote(string $politica, array $parametros): void
    {
        DB::table('produtos')->where('id', 1)->update(['quarentena' => $politica]);
        DB::table('status_produtos')->insert(['id' => 2, 'status' => 'APROVADO']);
        DB::table('acoes')->insert(['id' => 3, 'descricao' => 'Editar Produto']);
        $controller = new ProdutosController();
        $controller->store_mov_in($this->request($parametros));
        $lote = DB::table('produtos_mov_in')->first();
        $this->assertNotNull($lote);
        $novaPolitica = $politica === 'SIM' ? 'NAO' : 'SIM';
        $controller->update($this->request([
            'id_edit' => 1, 'nome' => 'Produto', 'tipo' => 'Teste', 'unidade_medida' => 'Teste',
            'qtd_aceitavel' => 20, 'qtd_minima' => 5, 'quarentena' => $novaPolitica,
        ]));
        $this->assertSame($novaPolitica, DB::table('produtos')->value('quarentena'));
        $this->assertEquals($lote, DB::table('produtos_mov_in')->where('id', $lote->id)->first());
        $this->assertCount($politica === 'SIM' ? 1 : 0, (new QuarentenaController())->index()->getData()['lotes']);
        $controller->store_mov_in($this->request());
        $this->assertSame([$politica, $novaPolitica], DB::table('produtos_mov_in')->orderBy('id')->pluck('quarentena')->all());
    }

    public function test_outro_status_continua_aceito_na_entrada(): void
    {
        $id = DB::table('status_produtos')->insertGetId(['status' => 'OUTRO']);
        (new ProdutosController())->store_mov_in($this->request(['status_lote' => $id]));
        $lote = DB::table('produtos_mov_in')->first();
        $this->assertNotNull($lote, session('alert-danger', 'Entrada não persistida'));
        $this->assertEquals($id, $lote->status_lote);
        $this->assertEquals(0, $lote->qtd_itens_estoque);
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
