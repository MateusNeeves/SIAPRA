<?php

// Smoke test do renderer real, sem Laravel, banco ou credenciais.
require __DIR__.'/../../vendor/autoload.php';

$diretorio = getenv('SIAPRA_PDF_QA_DIR') ?: __DIR__.'/../../storage/app/p01-p04-qa';
if (!is_dir($diretorio)) {
    mkdir($diretorio, 0777, true);
}
$pdf = new \Mpdf\Mpdf(['tempDir' => $diretorio.'/mpdf']);
$html = stream_get_contents(STDIN);
$pdf->WriteHTML($html);
$bytes = $pdf->Output('', 'S');
$leitor = new \setasign\Fpdi\PdfReader\PdfReader(new \setasign\Fpdi\PdfParser\PdfParser(
    \setasign\Fpdi\PdfParser\StreamReader::createByString($bytes)
));
$paginas = $leitor->getPageCount();
file_put_contents($diretorio.'/'.basename($argv[1]).'.pdf', $bytes);
file_put_contents($diretorio.'/'.basename($argv[1]).'.html', $html);
$fontes = [];
$conteudo = '';
for ($pagina = 1; $pagina <= $paginas; $pagina++) {
    $conteudo .= $leitor->getPage($pagina)->getContentStream();
}
preg_match_all('/\/F\w+\s+([\d.]+)\s+Tf/', $conteudo, $fontes);
echo json_encode(['paginas' => $paginas, 'bytes' => strlen($bytes),
    'menor_fonte' => $fontes[1] ? min(array_map('floatval', $fontes[1])) : null,
    'conteudo' => base64_encode($conteudo)]);
