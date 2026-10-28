<?php

namespace Tests\Support;

use Symfony\Component\Process\Process;

trait VerificaPdf
{
    private function verificarPdfReal(string $html, string $nome, int $minimoPaginas = 1): array
    {
        // Processo separado: os testes de controller usam overload de Mpdf para capturar HTML.
        $process = new Process([PHP_BINARY, __DIR__.'/render-pdf.php', $nome]);
        $process->setInput($html);
        $process->setTimeout(60);
        $process->mustRun();
        $resultado = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertGreaterThanOrEqual($minimoPaginas, $resultado['paginas']);
        $this->assertGreaterThan(1000, $resultado['bytes']);
        return $resultado;
    }
}
