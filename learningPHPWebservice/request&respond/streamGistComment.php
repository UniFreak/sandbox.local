<?php
include('githubToken.php');

$url = 'https://api.github.com/gists/whatever/comments';
$comments = json_encode(array('body' => 'this is a great comment'));
$options = array(
    'http' => array(
        'header' => array(
            'User-Agent: php-curl',
            'Content-Type: application/json',
            'Authorization: token ' . $githubToken,
            ),
        'method' => 'POST',
        'content' => $comments,
        )
    );
$response = file_get_contents($url, false, stream_context_create($options));
// @?: where is the var from
print_r($http_response_header);