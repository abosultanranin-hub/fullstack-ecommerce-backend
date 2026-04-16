<?php
$host = 'sandbox.smtp.mailtrap.io';
$port = 587;
$timeout = 10;
echo "Connecting to tcp://$host:$port...\n";
$errno = 0;
$errstr = '';
$socket = @stream_socket_client("tcp://$host:$port", $errno, $errstr, $timeout);
if (!$socket) {
    echo "ERROR: $errno - $errstr\n";
} else {
    echo "Connected!\n";
    $response = fgets($socket, 512);
    echo "Server said: $response\n";
    fclose($socket);
}
