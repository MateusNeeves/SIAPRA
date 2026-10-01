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

/**
 * Runs application queries/controllers and the actual Blade table, without migrations.
 * Default: SQLite in memory. TOTAL_ESTOQUE_PGSQL=1: local PostgreSQL, TEMP tables only.
 * Remaining characterization tests document server-data/session risks outside this fix.
 */
class TotalEstoqueTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $pgsql = getenv('TOTAL_ESTOQUE_PGSQL') === '1';
        if ($pgsql) {
            $this->assertContains(config('database.connections.pgsql.host'), ['localhost', '127.0.0.1', '::1']);
            config(['database.default' => 'pgsql', 'database.connections.pgsql.url' => null]);
            DB::purge('pgsql');
        } else {
            config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
                'database.connections.sqlite.url' => null]);
            DB::purge('sqlite');
            DB::connection()->getPdo()->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
        }
        DB::beginTransaction();
        if ($pgsql) {
            // No public-schema fallback: a missing fixture cannot touch a real table.
            DB::statement('SET LOCAL search_path TO pg_temp');
        }
        $id = $pgsql ? 'SERIAL PRIMARY KEY' : 'INTEGER PRIMARY KEY';
        foreach ([
            "status_produtos (id $id, status TEXT)",
            "fabricantes (id $id, nome TEXT)",
            "fornecedores (id $id, nome TEXT)",
            "tipos_produtos (id $id, nome TEXT)",
            "unidade_medida (id $id, nome TEXT)",
            "produtos (id $id, nome TEXT, descricao TEXT, id_tipo INTEGER, id_unidade_medida INTEGER, qtd_aceitavel INTEGER, qtd_minima INTEGER, quarentena TEXT)",
            'produtos_fab (id_produto INTEGER, id_fabricante INTEGER)',
            'produtos_forn (id_produto INTEGER, id_fornecedor INTEGER)',
            "produtos_mov_in (id $id, id_produto INTEGER, id_fabricante INTEGER, id_fornecedor INTEGER, lote_fabricante TEXT, qtd_itens_recebidos INTEGER, qtd_itens_estoque INTEGER, preco NUMERIC, data_entrega DATE, data_validade DATE, quarentena TEXT, status_lote INTEGER, descricao_status_lote TEXT)",
            "acoes (id $id, descricao TEXT)",
            "dest_produtos (id $id, nome TEXT)",
            "produtos_mov_out (id $id, id_produtos_mov_in INTEGER, id_destino INTEGER, qtd_itens_movidos INTEGER, data_mov_out DATE, motivo_saida INTEGER)",
            "logs (id $id, id_user INTEGER, id_acao INTEGER, tipo TEXT, descricao TEXT, data_hora TIMESTAMP)",
        ] as $table) {
            DB::statement('CREATE TEMPORARY TABLE '.$table);
        }
        foreach (['fabricantes', 'fornecedores', 'tipos_produtos', 'unidade_medida'] as $table) {
            DB::table($table)->insert(['id' => 1, 'nome' => 'Teste']);
        }
        DB::table('produtos')->insert(['id' => 1, 'nome' => 'Produto', 'id_tipo' => 1,
            'id_unidade_medida' => 1, 'quarentena' => 'NAO']);
        DB::table('status_produtos')->insert([
            ['id' => 41, 'status' => 'Aprovado'], ['id' => 77, 'status' => ' Áprovádo '],
            ['id' => 90, 'status' => 'REPROVADO'], ['id' => 91, 'status' => 'DESCARTADO'],
        ]);
        DB::table('acoes')->insert([
            ['id' => 1, 'descricao' => 'Movimentação (Entrada) de Produto'],
            ['id' => 2, 'descricao' => 'Movimentação (Saída) de Produto'],
        ]);
        DB::table('dest_produtos')->insert(['id' => 1, 'nome' => 'Saída']);
        Auth::shouldReceive('user')->andReturn((object) ['id' => 1]);
        Carbon::setTestNow(Carbon::parse('2026-10-01 12:00:00', 'America/Sao_Paulo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        DB::disconnect();
        parent::tearDown();
    }

    private function request(array $data = []): Request
    {
        return Request::create('/', 'POST', array_replace(['id_view' => 1], $data));
    }

    private function lote(array $data = []): int
    {
        return DB::table('produtos_mov_in')->insertGetId(array_replace([
            'id_produto' => 1, 'id_fabricante' => 1, 'lote_fabricante' => 'Teste',
            'qtd_itens_recebidos' => 100, 'qtd_itens_estoque' => 10,
            'data_validade' => '2026-10-02', 'quarentena' => 'NAO', 'status_lote' => 41,
        ], $data));
    }

    private function renderSession(): string
    {
        $source = file_get_contents(resource_path('views/produtos/visualizar.blade.php'));
        // Include the actual session assignments, not a manually constructed $lotesV.
        $vars = strpos($source, '@php', strpos($source, "@section('visualizar')"));
        $varsEnd = strpos($source, '@endphp', $vars) + strlen('@endphp');
        $start = strpos($source, '<!-- Lista de QTD em Estoque -->');
        $end = strpos($source, '</table>', $start) + strlen('</table>');
        $compiled = (new BladeCompiler(new Filesystem(), sys_get_temp_dir()))->compileString(
            substr($source, $vars, $varsEnd - $vars).substr($source, $start, $end - $start)
        );
        $__env = new class { use ManagesLoops; };
        ob_start();
        try {
            eval('?>'.$compiled);
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private function assertTotal(int $expected, ?int $rows = null): void
    {
        (new ProdutosController())->view($this->request());
        $html = $this->renderSession();
        $this->assertStringContainsString('Total: '.$expected.'</th>', $html);
        if ($rows !== null) {
            $this->assertCount($rows, session('lotesV'));
        }
    }

    private function selected(): array
    {
        session()->forget('lotesP');
        (new ProdutosController())->mov_out_select($this->request());
        return session('lotesP', []);
    }

    public static function eligibility(): array
    {
        return [
            'aprovado' => [[], 10, 1],
            'vence hoje' => [['data_validade' => '2026-10-01'], 10, 1],
            'vencido' => [['data_validade' => '2026-09-30'], 0, 0],
            'reprovado' => [['status_lote' => 90], 0, 0],
            'descartado' => [['status_lote' => 91], 0, 0],
            'outro ID aprovado com acentos' => [['status_lote' => 77], 10, 1],
            'zero' => [['qtd_itens_estoque' => 0], 0, 0],
            'negativo' => [['qtd_itens_estoque' => -5], 0, 0],
            'saldo numerico string' => [['qtd_itens_estoque' => '10'], 10, 1],
            'quarentena' => [['quarentena' => 'Sim'], 0, 0],
            'quarentena nula' => [['quarentena' => null], 0, 0],
            'quarentena vazia' => [['quarentena' => ''], 0, 0],
            'status nulo' => [['status_lote' => null], 0, 0],
            'status orfao' => [['status_lote' => 999], 0, 0],
        ];
    }

    #[DataProvider('eligibility')]
    public function test_query_session_blade_e_selecao(array $data, int $total, int $selected): void
    {
        $this->lote($data);
        $this->assertTotal($total, 1);
        $this->assertCount($selected, $this->selected());
    }

    public static function legacy(): array
    {
        return array_map(fn ($q) => [$q], ['NAO', 'Nao', 'Não', 'não', 'NÃO', ' nao ', ' Não ']);
    }

    #[DataProvider('legacy')]
    public function test_quarentena_legada(string $value): void
    {
        $this->lote(['quarentena' => $value]);
        $this->assertTotal(10);
        $this->assertCount(1, $this->selected());
    }

    public function test_multiplos_lotes_e_mistura(): void
    {
        $this->lote();
        $this->lote(['qtd_itens_estoque' => 15, 'status_lote' => 77]);
        $this->assertTotal(25, 2);
        foreach ([['status_lote' => 90], ['status_lote' => 91], ['quarentena' => 'SIM'],
            ['data_validade' => '2026-09-30'], ['qtd_itens_estoque' => 0]] as $data) {
            $this->lote($data);
        }
        $this->assertTotal(25, 7);
        $this->assertCount(2, $this->selected());
    }

    public function test_entrada_e_saida_reais_usam_saldo_atual(): void
    {
        $controller = new ProdutosController();
        $controller->store_mov_in($this->request(['status_lote' => 41, 'fabricante' => 'Teste',
            'fornecedor' => 'Teste', 'lote_fabricante' => 'Movimentado', 'qtd_itens_recebidos' => 25,
            'preco' => 1, 'data_entrega' => '2026-10-01', 'data_validade' => '2026-10-02']));
        $id = DB::table('produtos_mov_in')->value('id');
        $this->assertNotNull($id);
        $this->assertTotal(25);
        $controller->store_mov_out($this->request(['id_lote' => $id, 'destino' => 1,
            'qtd_itens_movidos' => 15, 'data_mov_out' => '2026-10-01']));
        $this->assertEquals(25, DB::table('produtos_mov_in')->value('qtd_itens_recebidos'));
        $this->assertEquals(10, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        $this->assertTotal(10);
    }

    public static function divergentNormalization(): array
    {
        return [
            'tab status' => ['status', "\tAprovado\n"],
            'tab quarentena' => ['quarentena', "\tNAO\n"],
            'outro acento quarentena' => ['quarentena', 'NÁO'],
            'acento decomposto status' => ['status', "Aprova\u{0301}do"],
            'acento decomposto quarentena' => ['quarentena', "Na\u{0303}o"],
            'status maiusculo' => ['status', 'APROVADO'],
            'status minusculo' => ['status', ' aprovado '],
            'status acentuado' => ['status', ' Ápróvâdõ '],
            'NBSP quarentena' => ['quarentena', "\u{00A0}Não\u{00A0}"],
            'todos os brancos status' => ['status', espacosTextoEnum().'Aprovado'.espacosTextoEnum()],
            'todos os brancos quarentena' => ['quarentena', espacosTextoEnum().'Não'.espacosTextoEnum()],
        ];
    }

    #[DataProvider('divergentNormalization')]
    public function test_normalizacao_php_sql_alinhada(string $field, string $value): void
    {
        if ($field === 'status') {
            DB::table('status_produtos')->where('id', 41)->update(['status' => $value]);
            $this->lote();
        } else {
            $this->lote([$field => $value]);
        }
        $this->assertTotal(10);
        $this->assertCount(1, $this->selected());
    }

    public function test_saldo_negativo_e_zero_nao_reduzem_total(): void
    {
        $this->lote(['qtd_itens_estoque' => 10]);
        $this->lote(['qtd_itens_estoque' => -5]);
        $this->assertTotal(10, 2);
        $selected = $this->selected();
        $this->assertCount(1, $selected);
        $this->assertEquals(10, $selected[0]['qtd_itens_estoque']);
        DB::table('produtos_mov_in')->where('qtd_itens_estoque', -5)->update(['qtd_itens_estoque' => 0]);
        $this->assertTotal(10, 2);
    }

    public static function saidasInvalidas(): array
    {
        return [
            'acima do saldo' => [[], 15, [], 'qtd_itens_movidos'],
            'zero' => [[], 0, [], 'qtd_itens_movidos'],
            'negativa' => [[], -1, [], 'qtd_itens_movidos'],
            'fracionaria' => [[], '1.5', [], 'qtd_itens_movidos'],
            'texto' => [[], 'abc', [], 'qtd_itens_movidos'],
            'ausente' => [[], null, [], 'qtd_itens_movidos'],
            'array' => [[], [1], [], 'qtd_itens_movidos'],
            'vencido' => [['data_validade' => '2026-09-30'], 1, [], 'id_lote'],
            'sem validade' => [['data_validade' => null], 1, [], 'id_lote'],
            'quarentena' => [['quarentena' => ' Sim '], 1, [], 'id_lote'],
            'quarentena desconhecida' => [['quarentena' => null], 1, [], 'id_lote'],
            'reprovado' => [['status_lote' => 90], 1, [], 'id_lote'],
            'descartado' => [['status_lote' => 91], 1, [], 'id_lote'],
            'status orfao' => [['status_lote' => 999], 1, [], 'id_lote'],
            'outro produto' => [['id_produto' => 2], 1, [], 'id_lote'],
            'lote inexistente' => [[], 1, ['id_lote' => 99999], 'id_lote'],
            'saldo zero' => [['qtd_itens_estoque' => 0], 1, [], 'qtd_itens_movidos'],
            'saldo negativo' => [['qtd_itens_estoque' => -5], 1, [], 'qtd_itens_movidos'],
        ];
    }

    #[DataProvider('saidasInvalidas')]
    public function test_saida_invalida_nao_grava_movimento_nem_altera_saldo(array $lote, mixed $quantidade, array $request, string $campo): void
    {
        $id = $this->lote($lote);
        $saldo = DB::table('produtos_mov_in')->where('id', $id)->value('qtd_itens_estoque');
        $nivel = DB::transactionLevel();
        try {
            (new ProdutosController())->store_mov_out($this->request(array_replace([
                'id_lote' => $id, 'destino' => 1, 'qtd_itens_movidos' => $quantidade,
                'data_mov_out' => '2026-10-01',
            ], $request)));
            $this->fail('Saída inválida deveria ser rejeitada.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($campo, $exception->errors());
        }
        $this->assertEquals($saldo, DB::table('produtos_mov_in')->where('id', $id)->value('qtd_itens_estoque'));
        $this->assertSame(0, DB::table('produtos_mov_out')->count());
        $this->assertSame(0, DB::table('logs')->count());
        $this->assertSame($nivel, DB::transactionLevel());
    }

    public function test_saida_valida_legada_e_segunda_tentativa_revalida_saldo(): void
    {
        $id = $this->lote(['status_lote' => 77, 'quarentena' => "\u{00A0}Não\n",
            'data_validade' => '2026-10-01']);
        $request = $this->request(['id_lote' => $id, 'destino' => 1,
            'qtd_itens_movidos' => '6', 'data_mov_out' => '2026-10-01']);
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = [$query->sql, $query->connection->transactionLevel()];
        });
        $response = (new ProdutosController())->store_mov_out($request);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertNotNull(session('alert-success'));
        $this->assertEquals(4, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        $this->assertSame(1, DB::table('produtos_mov_out')->count());
        $this->assertSame(1, DB::table('logs')->count());
        $this->assertStringContainsString('Total: 4</th>', $this->renderSession());
        if (DB::getDriverName() === 'pgsql') {
            $locks = array_filter($queries, fn ($q) => str_contains(strtolower($q[0]), 'for update'));
            $this->assertCount(1, $locks);
            $this->assertGreaterThan(1, array_values($locks)[0][1]); // Within controller transaction + test transaction.
            $lockIndex = array_key_first($locks);
            $writes = array_filter($queries, fn ($q) => str_starts_with(strtolower($q[0]), 'insert into "produtos_mov_out"'));
            $this->assertNotEmpty($writes);
            $this->assertLessThan(array_key_first($writes), $lockIndex);
        }
        try {
            (new ProdutosController())->store_mov_out($request);
            $this->fail('Uma segunda retirada de 6 não pode usar o saldo antigo de 10.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('qtd_itens_movidos', $exception->errors());
        }
        $this->assertEquals(4, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        $this->assertSame(1, DB::table('produtos_mov_out')->count());
    }

    public function test_falha_apos_decremento_reverte_saldo_movimento_e_log(): void
    {
        $id = $this->lote();
        DB::listen(function ($query) {
            if (str_starts_with(strtolower($query->sql), 'insert into "logs"')) {
                throw new \RuntimeException('Falha controlada depois de gravar o movimento e decrementar.');
            }
        });
        $response = (new ProdutosController())->store_mov_out($this->request([
            'id_lote' => $id, 'destino' => 1, 'qtd_itens_movidos' => 6, 'data_mov_out' => '2026-10-01',
        ]));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('Falha controlada', session('alert-danger'));
        $this->assertEquals(10, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        $this->assertSame(0, DB::table('produtos_mov_out')->count());
        $this->assertSame(0, DB::table('logs')->count());
        $this->assertSame(1, DB::transactionLevel());
    }

    public function test_caracteriza_fabricante_ausente_excluido_pelo_join(): void
    {
        $this->lote(['id_fabricante' => 999]);
        $this->assertEquals(10, DB::table('produtos_mov_in')->value('qtd_itens_estoque'));
        $this->assertTotal(0, 0); // Possible only if server data lacks the declared FK integrity.
    }

    public function test_caracteriza_fotografia_da_sessao(): void
    {
        $id = $this->lote();
        $this->assertTotal(10);
        DB::table('produtos_mov_in')->where('id', $id)->update(['qtd_itens_estoque' => 15]);
        $this->assertStringContainsString('Total: 10</th>', $this->renderSession());
        $this->assertTotal(15);
    }

    public function test_caracteriza_alias_status_ausente_na_fotografia(): void
    {
        $this->lote();
        $this->assertTotal(10);
        $lotes = session('lotesV');
        $this->assertSame('Aprovado', $lotes[0]['status_lote']);
        unset($lotes[0]['status_lote']);
        session()->put('lotesV', $lotes);
        $this->assertStringContainsString('Total: 0</th>', $this->renderSession());
        $this->assertStringContainsString('<td>10</td>', $this->renderSession());
    }

    public function test_caracteriza_validade_nula_interrompendo_renderizacao(): void
    {
        $this->lote(['data_validade' => null]);
        $this->expectException(\Carbon\Exceptions\InvalidFormatException::class);
        $this->assertTotal(0);
    }

    public function test_espaco_unicode_nao_exclui_status_aprovado(): void
    {
        $value = "\u{00A0}Aprovado\u{00A0}";
        DB::table('status_produtos')->where('id', 41)->update(['status' => $value]);
        $this->lote();
        $this->assertSame('APROVADO', normalizarStatusLote($value));
        $this->assertTotal(10, 1);
        $this->assertCount(1, $this->selected());
    }

    public function test_limite_documentado_sql_nao_replica_toda_transliteracao_php(): void
    {
        // Preserve PHP legacy support; SQL intentionally maps only the documented accents.
        DB::table('status_produtos')->where('id', 41)->update(['status' => 'Aprøvado']);
        $this->lote();
        $this->assertSame('APROVADO', normalizarStatusLote('Aprøvado'));
        $this->assertTotal(10);
        $this->assertCount(0, $this->selected());
    }

    public function test_string_numerica_na_sessao_soma_normalmente(): void
    {
        $this->lote();
        $this->assertTotal(10);
        $lotes = session('lotesV');
        $lotes[0]['qtd_itens_estoque'] = '10';
        session()->put('lotesV', $lotes);
        $this->assertStringContainsString('Total: 10</th>', $this->renderSession());
    }

    public function test_fuso_diferente_muda_elegibilidade_no_mesmo_instante(): void
    {
        $timezone = date_default_timezone_get();
        try {
            $instant = Carbon::parse('2026-10-02 01:00:00', 'UTC');
            $this->lote(['data_validade' => '2026-10-01']);
            date_default_timezone_set('America/Sao_Paulo');
            Carbon::setTestNow($instant->copy()->setTimezone('America/Sao_Paulo'));
            $this->assertTotal(10);
            $this->assertCount(1, $this->selected());
            date_default_timezone_set('UTC');
            Carbon::setTestNow($instant->copy());
            $this->assertTotal(0);
            $this->assertCount(0, $this->selected());
        } finally {
            date_default_timezone_set($timezone);
        }
    }

    public function test_postgresql_nao_lista_data_de_hoje_como_vencida(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Requires PostgreSQL DATE/timestamp comparison semantics.');
        }
        $this->lote(['data_validade' => '2026-10-01']);
        $this->assertTotal(10);
        (new ProdutosController())->view_expired();
        // PostgreSQL infers the bound parameter as DATE from the left-hand column.
        $this->assertCount(0, session('lotes_vencidos', []));
    }

    public function test_caracteriza_formato_de_data_incompativel_na_sessao(): void
    {
        $this->lote();
        $this->assertTotal(10);
        $lotes = session('lotesV');
        $lotes[0]['data_validade'] = '02/10/2026';
        session()->put('lotesV', $lotes);
        $this->expectException(\Carbon\Exceptions\InvalidFormatException::class);
        $this->renderSession();
    }
}
