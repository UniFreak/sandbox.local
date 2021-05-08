<?php
$options = [
    'location' => 'http://localhost:8080',
    'uri' => 'http://localhost'
];

$client = new SoapClient(null, $options);
print_r($client->getEvents());