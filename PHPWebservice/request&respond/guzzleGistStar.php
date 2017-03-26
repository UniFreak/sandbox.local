<?php
require '../vendor/autoload.php';
require 'githubToken.php';

$url = 'https://api.github.com/gists/whatever/star';

$client = new GuzzleHttp\Client();
$request = $client->createRequest('PUT', $url);
$request->setHeader('Authorization', 'token' . $githubToken);
$request->setHeader('Content-Length', '0');
$response = $client->send($request);

echo $response->getStatusCode();