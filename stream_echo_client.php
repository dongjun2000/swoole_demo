<?php

$resource = stream_socket_client('tcp://127.0.0.1:9501');

if (false === $resource) {
    die("Connect failed.\n");
}

fwrite($resource, 'Hello');

$content = stream_socket_recvfrom($resource, 1024);

echo $content;

fclose($resource);