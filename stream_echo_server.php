<?php

$resource = stream_socket_server('tcp://127.0.0.1:9501');

if (false === $resource) {
    die("Create failed\n");
}

while (true) {
    // 接受连接
    $conn = stream_socket_accept($resource);
    if (false !== $conn) {
        $content = fread($conn, 1024);
        var_dump($content);
        // 数据传输
        $msg = 'Welcome - ' . rand() . PHP_EOL;

        fwrite($conn, $msg);

        fclose($conn);
    }
}
