<?php

/**
 * Server 四层生命周期
 *
 * PHP完整的生命周期：
 *      执行PHP文件
 *          PHP扩展模块初始化（MINIT）
 *              PHP扩展请求初始化（RINIT）
 *              执行PHP逻辑
 *              PHP扩展请求结束（RSHUTDOWN）
 *              PHP脚本清理
 *          PHP扩展模块结束（MSHUTDOWN）
 *      终止PHP
 *
 * PHP请求生命周期：
 *      如果是 cli 执行 php 脚本，那么会完整执行整个过程，因为存在进程创建。
 *      如果是 php-fpm 请求响应阶段，那么会执行中间四步过程，等到 fpm 进程退出才执行扩展模块清理工作。
 *
 * Swoole Server 四层生命周期：
 *      程序全局期：
 *          Server->start 之前创建的对象资源，持续驻留内存，worker共享。
 *          全局期代码在 Server 结束时才会释放， reload无效。
 *
 *      进程全局期：
 *          Server 启动后创建多个进程，它们内存空间独立，非共享内存。
 *          worker 进程启动后 (onWorkerStart) 引入的代码在进程存活期有效， reload会重新加载。
 *
 *      会话期：
 *          在 onConnect 或第一次 onReceive 时创建， onClose 时销毁。
 *          客户端连接后创建的对象会常驻内存，直到此客户端离开才销毁。
 *
 *      请求期：
 *          在 onReceive / onRequest 收到请求开始，直到发送 Response 返回。
 *          请求期创建的对象会在请求完成后销毁，和 fpm 程序中的对象一样。
 *
 * 更多信息@doc：
 *      https://wiki.swoole.com/wiki/page/354.html
 */

// 1.程序全局期
$a = 'A';

$serv = new Swoole\Server('0.0.0.0', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$serv->set([
    'worker_num' => 2,
]);

$serv->on('WorkerStart', function ($server, $worker_id) {
    // 2.进程全局期
    echo "Worker {$worker_id} started\n";
});

$serv->on('Connect', function ($server, $fd) {
    // 3.会话期
    echo "Client {$fd} connect\n";
});

$serv->on('Receive', function (Swoole\Server $server, $fd, $reactor_id, $data) {
    // 4.请求期
    $server->send($fd, "aaaa\n");
});

$serv->on('Close', function ($server, $fd) {
    // 会话期结束
    echo "Client {$fd} closed\n";
});

$serv->start();
