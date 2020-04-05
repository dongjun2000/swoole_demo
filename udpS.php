<?php

/**
 * 创建一个UDP服务器
 *
 * 什么是 UDP？
 *      1.UDP 即用户数据报协议，是一个 无连接 协议。
 *      2.UDP 套接字是一种数据报套接字。
 *      3.UDP 数据报不能保证最终到达它们的目的地，不保证各个数据报的先后顺序跨网络后保持不变，也不保证每个数据报只到达一次。
 *      4.UDP 可以使全双工的。
 *      与 TCP 一样，UDP 既可以使用 IPv4，也可以使用 IPv6。
 *
 * 什么是 Swoole UDP Server？
 *      1.Swoole 底层实现的 UDP 协议的服务器，只能用于 cli 环境。
 *      2.使用方式和 Swoole TCP Server基本一致，根据 UDP 的特点，Server 相关操作的 API 不一样。
 *
 * Swoole UDP Server 特点：
 *      1.Swoole\Server 是异步服务器，所以是通过监听事件的方式来编写程序的。
 *      2.与 TCP Server 不同，UDP没有监听的概念，启动 Server 后，客户端无需 Connect，
 *              直接可以向 Server 监听的端口发送数据包，Server 对应事件为 onPacket。
 *      3.$clientInfo 是客户端的相关信息，是一个数组，有客户端 IP 和 端口等内容。
 *      4.调用 $server->sendto() 向客户端发送数据。
 *
 * Swoole UDP Server 参数：
 *      参数和 Swoole TCP Server 基本一致，只需要 $socket_type 指定为 SWOOLE_SOCK_UDP 类型。
 *
 * 备注：
 *      在容器里暴露 udp 端口需要指定类型，否则默认是 tcp类型。
 *      $ docker run -it -p 7748:7748/udp -v /home/ubuntu:/usr/share/nginx/html ImageID bash
 *
 *      终端连接 udp 服务器可以用 netcat 或 nc：
 *      $ netcat -u ip port
 *
 * 更多信息@doc：
 *      https://wiki.swoole.com/wiki/page/14.html
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
