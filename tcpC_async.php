<?php

/**
 * 异步非阻塞tcp客户端
 *
 * 高版本Swoole需要按照 ext-async 扩展，建议使用协程客户端。
 *
 * 异步客户端与同步TCP客户端不同，异步客户端是非阻塞的。可以用于编写高并发的程序。
 * Swoole官方提供的redis-async、mysql-async都是基于异步Swoole_client实现的。
 *
 * 异步客户端需要设置回调函数，有4个事件回调必须设置 onConnect、onError、onReceive、onClose。
 * 分别在客户端连接成功、连接失败、回收数据、连接关闭时触发。
 *
 * connect() 发起连接的操作会立即返回，不存在任何等待。
 * 当对应的IO事件完成后，Swoole底层会自动调用设置好的回调函数。
 */

use Swoole\Client;

$cli = new Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

// 注册连接成功回调
$cli->on('connect', function ($client) {
    $client->send('Hello\n');
});

// 注册数据接收回调
$cli->on('receive', function ($client, $data) {
    echo "Received: " . $data . "\n";
});

// 注册连接失败回调
$cli->on('error', function ($client) {
    echo "Connect failed\n";
});

// 注册连接关闭回调
$cli->on('close', function ($client) {
    echo "Connection close\n";
});

// 发起连接
$cli->connect('127.0.0.1', 9501, 0.5);
