<?php
require '../vendor/autoload.php';
require 'githubToken.php';

$url = 'https://api.github.com/gists/9376b77a6994b48356f5/comments';
$comment = json_encode(array('body' => 'This is a comment'));

$client = new GuzzleHttp\Client();
$request = $client->createRequest('POST', $url);
$request->setHeader('Authorization', 'token ' . $githubToken);
$request->setBody(\GuzzleHttp\Stream\Stream::factory($comment));
$response = $client->send($request);

echo $response->getStatusCode();