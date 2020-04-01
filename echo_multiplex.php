<?php

/**
 * IO多路复用
 */

// 创建
$resource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if (false === $resource) {
    die("Create failed\n");
}

// 设置
if (!socket_set_option($resource, SOL_SOCKET, SO_REUSEPORT, 1)) {
    die("Set option failed\n");
}

// 地址绑定
if (!socket_bind($resource, '127.0.0.1', 9501)) {
    die("Bind failed\n");
}

// 监听
// backlog：请求队列的最大等待个数
if (!socket_listen($resource, 2)) {
    die("Listen failed\n");
}

$read = $write = $except = [];
$client = [$resource];

while (true) {
    $read = $client;

    socket_select($read, $write, $except, null);

    foreach($read as $fd) {
        if ($fd == $resource) {
            $conn = socket_accept($resource);
            $client[] = $conn;
            echo "New client connected\n";
        } else {
            echo @socket_read($fd, 100);
        }
    }
}
