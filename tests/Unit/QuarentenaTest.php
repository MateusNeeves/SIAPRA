<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Concerns\ManagesLoops;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuarentenaTest extends TestCase
{
    public static function valores(): array
    {
        return [
            ['Sim', 'SIM'], ['sim', 'SIM'], ['SIM', 'SIM'],
            ['Não', 'NAO'], ['Nao', 'NAO'], ['não', 'NAO'],
            ['nao', 'NAO'], ['NAO', 'NAO'], ['NÃO', 'NAO'],
            [' Sim ', 'SIM'], [' Não ', 'NAO'],
        ];
    }

    #[DataProvider('valores')]
    public function test_normalizacao_e_validacao_preservam_o_valor_original(string $valor, string $esperado): void
    {
        $this->assertSame($esperado, normalizarQuarentena($valor));
        $request = new Request(['quarentena' => $valor]);
        $dados = array_replace($request->all(), array_map('normalizarQuarentena', $request->only('quarentena')));
        $factory = new Factory(new Translator(new ArrayLoader(), 'pt_BR'));
        $this->assertTrue($factory->make($dados, ['quarentena' => 'in:SIM,NAO'])->passes());
        $this->assertSame($valor, $request->input('quarentena'));
    }

    public function test_valores_invalidos_continuam_rejeitados(): void
    {
        $factory = new Factory(new Translator(new ArrayLoader(), 'pt_BR'));
        foreach (['Talvez', '0', '1', ['SIM']] as $valor) {
            $this->assertFalse($factory->make(
                ['quarentena' => normalizarQuarentena($valor)],
                ['quarentena' => 'in:SIM,NAO']
            )->passes());
        }
    }

    #[DataProvider('valores')]
    public function test_tabela_preserva_quantidade_validade_e_texto(string $valor, string $normalizado): void
    {
        $timezone = date_default_timezone_get();
        date_default_timezone_set('America/Sao_Paulo');
        try {
            foreach (['00:00:00', '12:30:00', '23:59:59'] as $hora) {
                Carbon::setTestNow(Carbon::parse('2026-09-15 '.$hora));
                foreach (['2026-09-14' => false, '2026-09-15' => true, '2026-09-16' => true] as $validade => $valido) {
                    $html = $this->renderTabela([[
                        'id' => 1, 'nome' => 'Fabricante', 'lote_fabricante' => 'Lote A',
                        'qtd_itens_estoque' => 100, 'data_validade' => $validade, 'quarentena' => $valor,
                    ]]);
                    $total = $normalizado === 'NAO' && $valido ? 100 : 0;
                    $this->assertStringContainsString('Total: '.$total.'</th>', $html);
                    foreach (['Lote A', '100', $validade, $valor] as $celula) {
                        $this->assertStringContainsString('<td>'.$celula.'</td>', $html);
                    }
                    $classe = $normalizado === 'SIM' ? 'table-danger' : '';
                    $this->assertStringContainsString('<tr class="'.$classe.'">', $html);
                }
            }
        } finally {
            Carbon::setTestNow();
            date_default_timezone_set($timezone);
        }
    }

    public function test_lista_vazia(): void
    {
        $html = $this->renderTabela([]);
        $this->assertStringContainsString('Nenhum lote encontrado', $html);
        $this->assertStringContainsString('Total: 0</th>', $html);
    }

    #[DataProvider('valores')]
    public function test_formulario_seleciona_a_opcao_sem_alterar_rotulos(string $valor, string $normalizado): void
    {
        $originalContainer = Container::getInstance();
        $container = new Container();
        Container::setInstance($container);
        $request = new Request();
        $session = new Store('quarentena-test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);
        $container->instance('request', $request);
        $source = file_get_contents(__DIR__.'/../../resources/views/produtos/visualizar.blade.php');
        $start = strpos($source, '<select id="quarentena"');
        $end = strpos($source, '</select>', $start) + strlen('</select>');
        $compiler = new BladeCompiler(new Filesystem(), sys_get_temp_dir());
        $compiled = $compiler->compileString(substr($source, $start, $end - $start));
        try {
            foreach ([false, true] as $oldInput) {
                $produto = (object) ['quarentena' => $oldInput ? '' : $valor];
                $session->put('_old_input', $oldInput ? ['quarentena' => $valor] : []);
                ob_start();
                try {
                    eval('?>'.$compiled);
                    $html = ob_get_contents();
                } finally {
                    ob_end_clean();
                }
                $rotulo = $normalizado === 'SIM' ? 'Sim' : 'Não';
                $this->assertStringContainsString('<option value="'.$rotulo.'" selected> '.$rotulo.' </option>', $html);
                $this->assertSame(1, substr_count($html, 'selected'));
            }
        } finally {
            Container::setInstance($originalContainer);
        }
    }

    private function renderTabela(array $lotesV): string
    {
        $source = file_get_contents(__DIR__.'/../../resources/views/produtos/visualizar.blade.php');
        $start = strpos($source, '<!-- Lista de QTD em Estoque -->');
        $end = strpos($source, '</table>', $start) + strlen('</table>');
        $compiler = new BladeCompiler(new Filesystem(), sys_get_temp_dir());
        $compiled = $compiler->compileString(substr($source, $start, $end - $start));
        $__env = new class {
            use ManagesLoops;
        };
        ob_start();
        try {
            eval('?>'.$compiled);
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
