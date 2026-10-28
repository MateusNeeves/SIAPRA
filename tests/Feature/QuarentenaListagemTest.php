<?php

namespace Tests\Feature;

use App\Http\Controllers\QuarentenaController;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Concerns\ManagesLoops;
use Tests\TestCase;

class QuarentenaListagemTest extends TestCase
{
    public function test_listagem_e_html_preservam_todos_os_lotes_apos_o_decimo(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
        foreach ([
            'produtos (id INTEGER PRIMARY KEY, nome TEXT, quarentena TEXT)',
            'fabricantes (id INTEGER PRIMARY KEY, nome TEXT)',
            'produtos_mov_in (id INTEGER PRIMARY KEY, id_produto INTEGER, id_fabricante INTEGER, lote_fabricante TEXT, data_entrega TEXT, data_validade TEXT, quarentena TEXT, status_lote TEXT, qtd_itens_estoque INTEGER)',
        ] as $table) {
            DB::statement('CREATE TABLE '.$table);
        }
        DB::table('fabricantes')->insert(['id' => 1, 'nome' => 'Fabricante']);
        for ($id = 1; $id <= 13; $id++) {
            DB::table('produtos')->insert(['id' => $id, 'nome' => 'Produto '.$id, 'quarentena' => 'SIM']);
            DB::table('produtos_mov_in')->insert([
                'id' => $id, 'id_produto' => $id, 'id_fabricante' => 1,
                'lote_fabricante' => 'Lote-teste-'.$id, 'data_entrega' => '2026-01-01',
                'data_validade' => $id % 2 ? '2020-01-01' : '2028-10-28',
                'status_lote' => ['APROVADO', 'REPROVADO', 'DESCARTADO'][$id % 3],
                'qtd_itens_estoque' => $id % 2 ? 0 : 4,
                'quarentena' => $id === 13 ? 'NAO' : ['SIM', ' Sim ', 'sim'][$id % 3],
            ]);
        }

        $lotes = (new QuarentenaController())->index()->getData()['lotes'];
        $this->assertCount(12, $lotes);
        $this->assertSame(range(1, 12), array_column($lotes, 'id_lote'));
        $this->assertSame(12, $lotes[11]['id_produto']);

        // Renderiza a tabela Blade real sem layout, autenticação ou banco externo.
        $source = file_get_contents(resource_path('views/quarentena/visualizar.blade.php'));
        $start = strpos($source, '<table id="myTableSelect"');
        $end = strpos($source, '</table>', $start) + strlen('</table>');
        $compiled = (new BladeCompiler(new Filesystem(), sys_get_temp_dir()))
            ->compileString(substr($source, $start, $end - $start));
        $__env = new class { use ManagesLoops; };
        ob_start();
        try {
            eval('?>'.$compiled);
            $html = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        foreach (range(1, 12) as $id) {
            $this->assertStringContainsString('Lote-teste-'.$id.' ', $html);
        }
        $this->assertStringNotContainsString('Lote-teste-13 ', $html);
    }
}
