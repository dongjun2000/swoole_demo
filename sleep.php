<?php

/**
 * 睡眠函数的影响
 *
 * sleep()、usleep()
 *
 * 1.睡眠函数会使工作进程陷入阻塞，直到指定时间后操作系统才会唤醒进程。
 * 2.Swoole Server 无法再处理新的请求。
 *
 * 处理方式：
 *      1.不使用睡眠/阻塞IO函数
 *      2.设置多个工作进程
 *      3.开启协程定时器调度
 */

// 网络客户端/睡眠函数协程化
//Swoole\Runtime::enableCoroutine(true);

// 创建一个server对象，设置监听IP和端口
$serv = new Swoole\Server('0.0.0.0', 9501);

// 单工作进程
$serv->set([
    'worker_num' => 1,
]);

$serv->on('Receive', function ($server, $fd, $reactor_id, $data) {
    // 验证有没有接收到客户端请求
    $server->send($fd, "Hello\n");

    // 睡眠5秒
    sleep(5);
//    co::sleep(5);
    $server->send($fd, "Swoole:{$data}\n");
});

// 开启服务器
$serv->start();
