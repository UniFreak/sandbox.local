<?php

$addr = gethostbyname("www.example.com");

$client = stream_socket_client("tcp://$addr:80", $errno, $errorMessage);

if ($client === false) {
    throw new UnexpectedValueException("Failed to connect: $errorMessage");
}

fwrite($client, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
echo stream_get_contents($client);
fclose($client);