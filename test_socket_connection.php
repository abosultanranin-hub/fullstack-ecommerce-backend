<?php
$host = 'sandbox.smtp.mailtrap.io';
$ports = [2525, 587, 25, 465];

foreach ($ports as $port) {
    echo "Testing connection to $host:$port... ";
    $connection = @fsockopen($host, $port, $errno, $errstr, 10);

    if (is_resource($connection)) {
        echo "SUCCESS! " . fgets($connection) . "\n";
        fclose($connection);
    } else {
        echo "FAILED. Error: $errstr ($errno)\n";
    }
}
