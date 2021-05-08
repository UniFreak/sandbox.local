<?php
require '../Events.php';

$server = new SoapServer('wsdl');
$server->setClass('Events');
$server->handle();