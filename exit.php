<?php

/**
 * 中止函数的影响
 *
 * exit()、die()
 *
 * 1.中止函数会使工作进程立即退出，异常退出会被 master 进程重新拉起，从而导致工作进程不断退出和拉起。
 * 2.产生大量的警报日志。
 * 3.导致当前进程内的其它任务被丢弃。
 *
 * 处理方式：
 *      1.不使用中止函数。
 *      2.最外层使用 try / catch 捕获异常。
 *
 * 使用 try/catch好处：
 *      1.当前工作进程不会退出，资源不会被销毁。
 *      2.当前工作进程依然可以处理其它任务。
 */

// 网络客户端/睡眠函数协程化
Swoole\Runtime::enableCoroutine(true);

$serv = new \Swoole\Server('0.0.0.0', 9501);

// 单工作进程
$serv->set([
    'worker_num' => 1,
]);

$serv->on('Receive', function ($server, $fd, $reactor_id, $data) {
    try {
        // 验证有没有接收到客户端请求
        $server->send($fd, "Hello\n");

        // 睡眠
        sleep(5);

        // 10 % 0;
        throw new \Exception('aaa');

        $server->send($fd, "Swoole: {$data}\n");
    } catch (\Throwable $e) {
        echo $e->getMessage() . ' ! ' . PHP_EOL;
    }
});

$serv->start();