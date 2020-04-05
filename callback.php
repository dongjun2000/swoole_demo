<?php

/**
 * 事件回调函数详解
 *
 * Swoole\Server 是事件驱动模式，所有的业务逻辑代码必须写在事件回调函数中。
 * 当特定的网络事件发生后，底层会主动回调指定的PHP函数。
 *
 * 更多信息@doc：
 *      https://wiki.swoole.com/wiki/page/41.html
 *
 * 事件执行顺序：
 *      1.所有事件回调均在 Server start 后发生。
 *      2.服务器关闭终止时最后一次事件是 onShutdown。
 *      3.服务器启动成功后， onStart / onManagerStart / onWorkerStart 会并发执行。
 *      4.onReceive / onConnect / onClose 在 worker 进程中触发。
 *      5.Worker / Task 进程启动和结束会分别调用 onWorkerStart / onWorkerStop。
 *      6.onTask 事件仅在 task 进程中发生。
 *      7.onFinish 事件仅在 worker 进程中发生。
 */

// 1.程序全局期： start 之前创建好的对象
// 变量在启动后一直存在，直到整个程序结束运行才会销毁，reload 无效
// 在 Worker 进程内对这些对象进行写操作时，会自动从共享内存中分离，变成进程全局对象

use Swoole\Server;

Swoole\Runtime::enableCoroutine(true);
$serv = new Server('0.0.0.0', 9501);

$serv->set([
    'worker_num' => 2,
]);

// onStart、onManagerStart、onWorkerStart 是并发执行的

$serv->on('Start', function (Server $server) {
});

// 管理进程启动时调用
$serv->on('ManagerStart', function (Server $server) {
    // 触发时说明 task 和 worker 进程已创建，master 状态不明，manager 与 master 是并行的
});

$serv->on('WorkerStart', function (Server $server, int $worker_id) {
    // 2.进程全局期
    // 子进程存活周期之内，是常驻内存的，进程期 include 的文件在 reload 后就会重新加载
});

$serv->on('ManagerStop', function (Server $server) {
    // 触发时说明 task 和 worker 进程已结束，已被 manager 进程回收
});

$serv->on('WorkerStop', function (Server $server, int $worker_id) {
    echo "worker stop\n";
});

// worker / task 进程发生异常后会在 manager 进程中回调
$serv->on('WorkerError', function (Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal) {
    // 用于报警和监控，遇到进程异常退出提示开发者进行处理
});

// 实际开启 reload_async 后，杀死worker进程并不会回调输出
//$serv->on('WorkerExit', function (swoole_server $server, int $worker_id) {
//echo "worker exit\n";
//});

// UDP协议下只有 onReceive 事件，没有 onConnect/onClose 事件
// 当设置 dispatch_mode=1 / 3 时会自动去掉 onConnect/onClose 事件回调

// 发生在 worker 进程中
$serv->on('Connect', function (Server $server, int $fd, int $reactor_id) {
    echo "onConnect\n";
    // 3.会话期
    // 会话期是在 onConnect 后创建，或者在第一次 onReceive 时创建，onClose 时销毁
    // 一个客户端连接进入后，创建的对象会常驻内存，直到此客户端离开才会销毁
});

// 发生在 worker 进程中
$serv->on('Receive', function (Server $server, int $fd, int $reactor_id, string $data) {
    echo "onReceive\n";
    // 4.请求期
    // onReceive 收到请求开始处理，直到返回结果发送 response，周期内创建的对象在请求完成后销毁
});

// 发生在 worker 进程中
$serv->on('Close', function (Server $server, int $fd, int $reactor_id) {
    // TCP 客户端连接关闭后 worker 进程中回调
    // 服务器主动关闭时，reactorId会设为-1，可以通过判断 < 0 分辨关闭是由哪端发起的
    echo "onClose\n";
});

// 发生在 worker 进程中
$serv->on('Packet', function (Server $server, string $data, array $client_info) {
    // 接收到 UDP 数据包时回调此函数
    echo "onPacket\n";
});

// Http Server 不接受 onConnect, onReceive 事件回调，取而代之是 onRequest 事件类型
$serv->on('Request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    // 4.请求期
    // 使用协程后事件回调函数将会并发地执行。
    // 协程是一种用户态线程实现，没有额外的调度消耗，仅占用内存.
    // 使用协程模式，可以理解为“每次事件回调函数都会创建一个新的线程去执行，事件回调函数执行完成后，线程退出”
    $response->end('<h1>a</h1>');
});

// worker / task 进程收到由 sendMessage 发送的管道消息时会触发
$serv->on('PipeMessage', function (Server $server, int $src_worker_id, $message) {
});

// 仅在 task 进程中发生
// V4.2.12 起，如果开启 task_enable_coroutine，则回调函数原型是:
// function (Server $server, Swoole\Server\Task $task) {
//      $task->worker_id, $task->id, $task->flags, $task->data, $task->finish([123, 'hello']);
// }
$serv->on('Task', function (Server $server, int $task_id, int $src_worker_id, $data) {
    // $taskId 和 $src_worker_id 组合起来才是全局唯一的
    // 可以通过 $server->finish($response) 或者 return '' 来触发 onFinish 事件回调
    // 如果 worker 不关心任务执行结果，不需要 return 或 finish.
});

// 仅在 worker 进程中发生
// 和下发 task 的是同一进程
$serv->on('Finish', function (Server $server, int $task_id, string $data) {
});

// 调用事件之前，底层已销毁所有进程、线程、监听端口
// 强制 kill 和 Ctrl+C 进程不会回调，需要 kill -TERM
$serv->on('Shutdown', function (Server $server) {
});

$serv->start();
