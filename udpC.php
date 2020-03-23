<?php

/**
 * 创建一个UDP客户端
 */

use Swoole\Client;

// 创建一个UDP的客户端对象
$cli = new Client(SWOOLE_SOCK_UDP);

// 客户端无需Connect，直接可以向Server监听的端口发送数据包。
if(!$cli->sendto('127.0.0.1', 9501, "hello udp\n")) {
    die('send failed.');
}

if (!$data = $cli->recv()) {
    die('recv failed.');
}

echo $data;