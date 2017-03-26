<?php
$curl = curl_init('https://api.github.com/users/lornajane-demo/gists');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: php-curl'));

$response = curl_exec($curl);
$info = curl_getinfo($curl);

if ($info['http_code'] == 200) {
    echo $response;

    $data = json_decode($response, true);
    foreach ($data as $gist) {
        echo $gits['description'] . ": " . $gist['url'] . "\n";
    }
} else {
    echo 'Curl error:' . curl_error($curl);
}

curl_close($curl);