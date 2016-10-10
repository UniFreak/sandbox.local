<?php
inclue('githubToken.php');

$curl = curl_init('https://api.github.com/gists/whatever/star');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'User-Agent: php-curl',
    'Authorization: token ' . $githubToken,
    'Content-Length: 0',
    ));
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');

$response = curl_exec($curl);