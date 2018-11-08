<?php 
$ch = curl_init("http://baidu.com");
$file = fopen('./fetched', 'w');

curl_setopt($ch, CURLOPT_FILE, $file);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
fclose($file);