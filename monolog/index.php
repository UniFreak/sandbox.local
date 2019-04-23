<?php
require 'vendor/autoload.php';

use Monolog\Logger;
use Processor\TimerProcessor;
use Monolog\Handler\TestHandler;
use Monolog\Processor\MemoryUsageProcessor;

$logger = new Logger('exciler');
$handler = new TestHandler();
$logger->pushHandler($handler);
$logger->pushProcessor(new TimerProcessor());

$logger->info('test log', ['here' => 'we are']);
sleep(2);
$logger->info('test log 2');
sleep(3);
$logger->info('test log 3');

var_dump($handler->getRecords());
