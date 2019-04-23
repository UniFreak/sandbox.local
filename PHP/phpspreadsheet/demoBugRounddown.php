<?php
require 'vendor/autoload.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->setCellValue('A1','30000');
$worksheet->setCellValue('A2','0.0042');
$worksheet->setCellValue('A3','3');
$worksheet->setCellValue('A4','=ROUNDDOWN(A1*A2*A3,0)');

echo $worksheet->getCell('A4')->getCalculatedValue();