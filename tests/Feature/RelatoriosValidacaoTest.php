<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RelatoriosValidacaoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // Usuário em memória: autenticação e middleware reais, nenhuma consulta de usuário.
        $this->actingAs(new class(['username' => 'Teste']) extends User {
            public function getClassNamesAttribute() { return ['Admin']; }
        });
        DB::shouldReceive('select')->never();
    }

    public static function invalidos(): array
    {
        return [[null], ['0'], ['13'], ['1.5'], ["1' OR 1=1 --"]];
    }

    #[DataProvider('invalidos')]
    public function test_redireciona_com_erro_e_renderiza_mensagem_acessivel($meses): void
    {
        $response = $this->from(route('relatorios'))->get(route('relatorios.generate', [
            'tipo_relatorio' => 'itens_a_vencer', 'meses' => $meses,
        ]));
        $response->assertRedirect(route('relatorios'))->assertSessionHasErrors('meses');
        $mensagem = session('errors')->first('meses');
        $pagina = $this->get(route('relatorios'));
        $pagina->assertOk()->assertSee($mensagem)
            ->assertSee('id="meses-erro"', false)
            ->assertSee('aria-describedby="meses-erro"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('role="alert"', false);
        $dom = new \DOMDocument();
        @$dom->loadHTML($pagina->getContent());
        $this->assertStringNotContainsString('hidden', $dom->getElementById('meses_container')->getAttribute('class'));
        $this->assertSame($meses === null ? '1' : (string) $meses, $dom->getElementById('meses')->getAttribute('value'));
    }

    public function test_formulario_inicial_sem_erro(): void
    {
        $pagina = $this->get(route('relatorios'))->assertOk()
            ->assertDontSee('id="meses-erro"', false)
            ->assertDontSee('aria-invalid="true"', false);
        $dom = new \DOMDocument();
        @$dom->loadHTML($pagina->getContent());
        $this->assertSame('1', $dom->getElementById('meses')->getAttribute('value'));
        $this->assertStringContainsString('hidden', $dom->getElementById('meses_container')->getAttribute('class'));
        $this->assertTrue($dom->getElementById('tipo_relatorio')->getElementsByTagName('option')->item(0)->hasAttribute('selected'));
    }
}
