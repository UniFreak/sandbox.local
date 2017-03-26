<?php
$githubKey = '';
$comment = json_encode(array('body' => 'I made a comment'));

$curl = curl_init('https://api.github.com/gits/whatever/comments');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $comment);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'User-Agent: php-curl',
    'Authorization: token ' . $githubToken
    ));

$response = curl_exec($curl);
$info = curl_getinfo($curl);
if ($info['http_code'] == 201) {
    // all fine
}

curl_close($curl);