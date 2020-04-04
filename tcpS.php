<?php

/**
 * 创建 TCP 服务器
 *
 * 什么是 TCP？
 *      1.TCP 是传输控制协程。
 *      2.TCP 套接字是一种流套接字。
 *      3.TCP 关心"确认、超时和重传"之类的细节。
 *      大多数互联网应用程序使用 TCP，TCP 既即可使用 IPv4，也可以使用 IPv6。
 *
 * 什么是 Swoole TCP Server？
 *      1.Swoole底层实现的 TCP 协议的服务器，只能用于 cli 环境。
 *      2.默认使用 SWOOLE_PROCESS 模式，因此除了 worker 进程外，会创建额外 master 和 manager 两个进程。
 *      3.服务器启动后，通过 `kill 主进程id` 来结束所有工作进程。
 *
 * Swoole TCP Server 特点：
 *      1.Swoole\Server 是异步服务器，所以是通过监听事件的方式来编写程序的。
 *      2.当有新的 TCP 连接进入时会执行 onConnect 事件回调，当某个连接向服务器发送数据时会回调 onReceive 事件回调，客户端断开会触发 onClose 事件回调。
 *      3.$fd 是客户端连接的唯一标识（注意这个不是应用中的业务用户ID）。
 *      4.调用 $server->send() 向客户端连接发送数据。
 *      5.调用 $server->close() 可以强制关闭某个客户端连接。
 *
 * Swoole TCP Server 参数：
 *      $host： 监听的IP地址（支持IPv4和IPv6）
 *      $port： 监听的端口（监听小于1024端口需要root权限，0表示随机）
 *      $mode： 运行模式（支持 SWOOLE_PROCESS 和 SWOOLE_BASE）
 *      $socket_type： Socket的类型($socket_type | SWOOLE_SSL 启用 SSL加密，启用SSL后必须配置 ssl_key_file 和 ssl_cert_file)
 *
 * 更多信息@doc：
 *      https://wiki.swoole.com/wiki/page/14.html
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