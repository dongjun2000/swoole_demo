<?php

$address = gethostbyname('www.mi360.cn');

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if (!socket_connect($socket, $address, 9501)) {
    die('socket_connect() failed.');
}

$in = "hello\n";
socket_write($socket, $in, strlen($in));

while ($out = socket_read($socket, 2048)) {
    echo $out;
}

socket_close($socket);