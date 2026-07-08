<?php
require __DIR__ . '/../../vendor/autoload.php';
$pdf = new \setasign\Fpdi\TcpdfFpdi('L', 'mm', 'A4', true, 'UTF-8', false);
$pageCount = $pdf->setSourceFile(__DIR__ . '/../../public/assets/certificado/Certificado.pdf');
echo "pages={$pageCount}\n";
$tplId = $pdf->importPage(1);
$size = $pdf->getTemplateSize($tplId);
print_r($size);
