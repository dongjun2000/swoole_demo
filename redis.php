<?php

/**
 * Redis服务器
 *
 * Swoole\Redis\Server 继承自 Swoole\Server，一个兼容 Redis 服务器端协议的 Server 程序。
 *
 * Swoole\Redis\Server 不需要设置 onReceive 回调。
 *
 * 可用客户端：
 *      1.任意编程语言的 Redis 客户端，包括 PHP 的 Redis 扩展和库。
 *      2.Swoole 扩展提供的异步 Redis 客户端。
 *      3.Redis 提供的命令行工具，包括 redis-cli。
 *
 * 提供方法：
 *      父类的所有方法和以下新增：
 *          setHandler      设置 Redis 命令字的处理器
 *          format          格式化命令响应数据
 *
 * 提供常量：
 *      主要用于 format 函数打包 Redis 响应数据:
 *          Server::NIL     返回 nil 数据
 *          Server::ERROR   返回错误码
 *          Server::STATUS  返回状态
 *          Server::INT     返回整数
 *          Server::STRING  返回字符串
 *          Server::SET     返回列表（数组）
 *          Server::MAP     返回 Map（关联数组）
 *
 */

use Swoole\Redis\Server;

$serv = new Server('0.0.0.0', 9501);

$serv->strings = [];

// 设置命令字处理器
$serv->setHandler('swset', function ($fd, $data) use ($serv) {
    $key = $data[0];
    $val = $data[1];
    $serv->strings[$key] = $val;
    // 格式化返回数据
    $serv->send($fd, Server::format(Server::STRING, 'OK'));
});

$serv->setHandler('swget', function ($fd, $data) use ($serv) {
    $key = $data[0];
    $val = $serv->strings[$key];
    $serv->send($fd, Server::format(Server::STRING, $val));
});

$serv->start();

