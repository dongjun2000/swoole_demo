<?php

// 创建
$resource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if (false === $resource) {
    die("Create failed\n.");
}

// 设置
if (!socket_set_option($resource, SOL_SOCKET, SO_REUSEPORT, 1)) {
    die("Set option failed\n");
}

// 连接
if (!socket_connect($resource, '127.0.0.1', 9501)) {
    die("Connect failed\n");
}

// 数据传输
echo socket_read($resource, 100, PHP_NORMAL_READ);

// 关闭
socket_close($resource);
