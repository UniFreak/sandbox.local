<?php
/**
 * nonWSDL mode server
 *
 * 1. cd to this file's parent directory
 * 2. run with `php -S localhost:8080 server.php`
 * 3. then open and build `client.php` to see the result
 */

require "../Events.php";

$options = ['uri' => 'http://localhost'];
$server = new SoapServer(null, $options);
$server->setClass('Events');
$server->handle();