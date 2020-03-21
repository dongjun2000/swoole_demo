<?php

/**
 * 创建一个TCP服务器
 */

use Swoole\Server;

// 监听ip和端口
$host = '0.0.0.0';
$port = 9501;

// Server是异步服务器，所以是通过监听事件的方式来编写程序的。
// 创建一个server对象，设置监听ip和端口、运行模式 和 协议类型
$serv = new Server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

// 设置运行时参数
$serv->set([
    'worker_num' => 4,
]);

// 注册监听事件

// 发生连接事件
// 服务器可以同时被成千上万个客户端连接，$fd就是客户端连接的唯一标识符
// 参数： $server由Swoole底层触发事件回调函数时传入
$serv->on('Connect', function (Server $server, int $fd) {
    echo "Client-{$fd}: Connect.\n";
});

// 接收到数据事件
$serv->on('Receive', function (Server $server, int $fd, int $reactor_id, string $data) {
    echo "接收到数据: {$data}\n";
    // send($fd, $data)：方法向客户端连接发送数据，参数就是$fd客户端标识符
    $server->send($fd, "Server: " . $data);

    // close($fd)： 方法可以强制关闭某个客户端连接，服务端主动断开，也会回调onClose事件
    // $server->close($fd, true);
});

// 断开连接事件
$serv->on('Close', function (Server $server, int $fd) {
    echo "Client-{$fd}: Close.\n";
});

// 启动服务器
$serv->start();