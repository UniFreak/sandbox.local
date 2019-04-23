<?php
date_default_timezone_set('UTC');
require 'vendor/autoload.php';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setReadDataOnly(true);

$spreadsheet = $reader->load("test_file2.xlsx");

$worksheet = $spreadsheet->setActiveSheetIndexByName('控制台');

$worksheet->setCellValue('C1', 300000);
var_dump($worksheet->getCell('C1')->getCalculatedValue());
var_dump($worksheet->getCell('C19')->getCalculatedValue());
var_dump($worksheet->getCell('C41')->getCalculatedValue());
var_dump($worksheet->getCell('C42')->getCalculatedValue());
