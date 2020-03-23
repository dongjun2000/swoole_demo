<?php

/**
 * 创建一个UDP服务器
 *
 * UDP服务器与TCP服务器不同，UDP没有连接的概念。
 * 启动Server后，客户端无需Connect，直接可以向Server监听的端口发送数据包。对应的事件为 onPacket。
 */

use Swoole\Server;

// 创建 Server对象，监听ip:port端口，类型为SWOOLE_SOCK_UDP
$serv = new Server('127.0.0.1', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

/**
 * 接收到UDP数据包时回调此函数，发生在worker进程中。
 *
 * 参数：
 * $server： Server对象
 * $data：收到的数据内容，可能是文本或者二进制内容
 * $client_info：客户端信息包括 address/port/server_socket 等多项客户端信息数据
 */
$serv->on('Packet', function (Server $server, string $data, array $client_info) {
    // sendto($ip, $port, $data) 向任意的客户端 ip:port 发送 udp 数据包
    $server->sendto($client_info['address'], $client_info['port'], "Server: " . $data);
    var_dump($client_info);
});

// 启动服务器
$serv->start();
