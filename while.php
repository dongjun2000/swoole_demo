<?php

/**
 * 死循环的影响
 *
 * while(true){}
 *
 * 1.死循环取得控制权后，IO事件回调函数无法触发，无法再收到客户端请求。
 * 2.必须等待循环结束才能继续处理新的事件。
 *
 * 处理方式：
 *      1.处理程序中的死循环。
 *      2.增加工作进程提高处理能力。
 */

Swoole\Runtime::enableCoroutine(true);

$serv = new \Swoole\Server('0.0.0.0', 9501);

// 设置运行时参数
$serv->set([
    'worker_num' => 1,
]);

// 收到数据时触发
$serv->on('Receive', function ($server, $fd, $reactor_id, $data) {
    $server->send($fd, "hello\n");

    // 死循环
    $i = 0;
    while (true) {
        $i++;
    }

    $server->send($fd, "Swoole: {$data}\n");
});

$serv->start();
