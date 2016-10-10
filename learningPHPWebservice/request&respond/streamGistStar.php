<?php
include('githubToken.php');

$url = 'https://api.github.com/gists/whatever/star';
$options = array(
    'http' => array(
        'header' => array(
            'User-Agent: php-curl',
            'Content-Length: 0',
            'Authorization: token ' . $githubToken,
            ),
        'method' => 'PUT',
        )
    );
$response = file_get_contents($url, false, stream_context_create($options));

// 204 expected so no response - empty rather than false
// print_r($http_response_header);