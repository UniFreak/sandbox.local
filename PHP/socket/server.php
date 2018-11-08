<?php
/**
 * run `php server.php` to start this server
 * run `echo "Hello World" | nc 127.0.0.1 1337` to see the echo response
 */
$server = stream_socket_server("tcp://127.0.0.1:1337", $errno, $errorMessage);

if ($server === false) {
    throw new UnexpectedValueException("Could not bind to socket: $errorMessage");
}

for (;;) {
    $client = @stream_socket_accept($server);

    if ($client) {
        stream_copy_to_stream($client, $client);
        fclose($client);
    }
}