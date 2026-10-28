<?php

namespace Tests\Feature;

use App\Http\Controllers\ProdutosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuantidadeProdutoTest extends TestCase
{
    use \Tests\Support\PostgresTemporario;

    protected function tearDown(): void
    {
        $this->encerrarPostgresTemporario();
        parent::tearDown();
    }

    private function campo(string $campo, object $produto): string
    {
        $source = file_get_contents(resource_path('views/produtos/visualizar.blade.php'));
        preg_match('/<x-text-input id="'.$campo.'"[^\r\n]+\/>/', $source, $match);
        return Blade::render($match[0], ['produto' => $produto]);
    }

    public function test_blade_preserva_old_e_normaliza_apenas_valores_do_banco(): void
    {
        $this->app['request']->setLaravelSession(session()->driver());
        $produto = (object) ['qtd_aceitavel' => '9999999.999', 'qtd_minima' => '1.500'];
        foreach (['qtd_aceitavel' => '9999999.999', 'qtd_minima' => '1.5'] as $campo => $valor) {
            $html = $this->campo($campo, $produto);
            $this->assertStringContainsString('value="'.$valor.'"', $html);
            $this->assertStringContainsString('type="number"', $html);
            $this->assertStringContainsString('step="0.001"', $html);
        }
        session()->flashInput(['qtd_minima' => '2.750']);
        $this->assertStringContainsString('value="2.750"', $this->campo('qtd_minima', $produto));
        $source = file_get_contents(resource_path('views/produtos/visualizar.blade.php'));
        preg_match_all('/<x-input-label[^>]+formatarQuantidade\([^\n]+\/>/', $source, $matches);
        $html = Blade::render(implode('', $matches[0]), ['produtoV' => $produto]);
        $this->assertStringContainsString('9999999,999', $html);
        $this->assertStringContainsString('1,5', $html);
    }

    public function test_listagem_e_envio_sem_alteracoes_preservam_decimal_persistido(): void
    {
        $this->iniciarPostgresTemporario();
        foreach ([
            'produtos (id INTEGER PRIMARY KEY, nome TEXT, descricao TEXT, id_tipo INTEGER, id_unidade_medida INTEGER, qtd_aceitavel NUMERIC(10,3), qtd_minima NUMERIC(10,3), quarentena TEXT)',
            'tipos_produtos (id INTEGER PRIMARY KEY, nome TEXT)',
            'unidade_medida (id INTEGER PRIMARY KEY, nome TEXT)',
            'fabricantes (id INTEGER PRIMARY KEY, nome TEXT)',
            'fornecedores (id INTEGER PRIMARY KEY, nome TEXT)',
            'produtos_fab (id_produto INTEGER, id_fabricante INTEGER)',
            'produtos_forn (id_produto INTEGER, id_fornecedor INTEGER)',
        ] as $table) {
            DB::statement('CREATE TEMPORARY TABLE '.$table);
        }
        foreach (['tipos_produtos', 'unidade_medida'] as $table) {
            DB::table($table)->insert(['id' => 1, 'nome' => 'Teste']);
        }
        DB::table('produtos')->insert(['id' => 1, 'nome' => 'Produto', 'descricao' => 'Descrição',
            'id_tipo' => 1, 'id_unidade_medida' => 1, 'qtd_aceitavel' => '9999999.999',
            'qtd_minima' => '1.500', 'quarentena' => 'NAO']);
        $antes = DB::table('produtos')->first();
        $controller = new ProdutosController();
        $infos = $controller->index()->getData()['produtos'];
        $this->assertSame('9999999,999', $infos[0]['qtd_aceitavel']);
        $this->assertSame('1,5', $infos[0]['qtd_minima']);
        $source = file_get_contents(resource_path('views/produtos/layout.blade.php'));
        preg_match('/@foreach \(\$infos as \$info\)(.*?)@endforeach/s', $source, $match);
        $html = Blade::render($match[0], ['infos' => $infos, 'indexes' => ['qtd_aceitavel', 'qtd_minima']]);
        $this->assertStringContainsString('>9999999,999</td>', $html);
        $this->assertStringContainsString('>1,5</td>', $html);
        $dados = [];
        foreach (['qtd_aceitavel', 'qtd_minima'] as $campo) {
            preg_match('/value="([^"]*)"/', $this->campo($campo, $antes), $match);
            $dados[$campo] = $match[1];
        }
        $response = $controller->update(Request::create('/', 'POST', $dados + [
            'id_edit' => 1, 'nome' => 'Produto', 'descricao' => 'Descrição',
            'tipo' => 'Teste', 'unidade_medida' => 'Teste', 'quarentena' => 'NAO',
        ]));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertNotNull(session('alert-success'));
        $this->assertNull(session('alert-danger'));
        $depois = DB::table('produtos')->first();
        $this->assertSame($antes->qtd_aceitavel, $depois->qtd_aceitavel);
        $this->assertSame($antes->qtd_minima, $depois->qtd_minima);
    }
}
