<?php
require 'vendor/autoload.php';
require '../Events.php';

$generator = new \PHP2WSDL\PHPCLASS2WSDL('Events', 'http://localhost:8080');
$generator->generateWSDL();

file_put_contents("wsdl", $generator->dump());