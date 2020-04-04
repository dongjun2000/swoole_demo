<?php

/**
 * 进程隔离
 *
 * 进程隔离是为保护系统中进程互不干扰而设计的，进程彼此的内存空间是独立的。
 *
 * 进程隔离意味着什么？
 *      1.多进程程序进程间都是彼此隔离的，比如我们自己用 fork 实现的。
 *      2.Swoole的多进程间必然也是彼此隔离的。
 *
 * 进程间如何通信？
 *      1.传统进程间通信方式有：管道、共享内存、消息队列、信号量、网络套接字
 *          （共享内存方式可参考：shmop.php）
 *      2.Swoole提供的方式有：Swoole\Table、Swoole\Atomic
 */

$i = 0;

$serv = new Swoole\Server('0.0.0.0', 9501);

$serv->set([
    'worker_num' => 2,
]);

// global 和 &引用 在子进程中都是隔离的。
$serv->on('Receive', function ($server, $fd, $reactor_id, $data) use (&$i) {
//    global $i;

    $i++;

    // 验证有没有接收到客户端请求
    $server->send($fd, "{$i}\n");
});

$serv->start();