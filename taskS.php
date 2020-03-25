<?php

/**
 * 执行异步任务
 *
 * 在Server程序中如果需要执行很耗时的操作，比如：一个聊天服务器发送广播，Web服务器中发送邮件。
 * 如果直接去执行这些耗时任务的函数就会阻塞当前进程，导致服务器响应变慢。
 *
 * Swoole 提供了异步任务处理的功能，可以投递一个异步任务到 TaskWorker 进程池中执行，不影响当前请求的处理速度。
 */

use Swoole\Server;

// 创建一个server对象
$serv = new Server('127.0.0.1', 9501);

// 设置运行时参数
$serv->set([
    'worker_num' => 4,
    'task_worker_num' => 4,
]);

// 接收到数据事件
$serv->on('receive', function (Server $server, int $fd, int $reactor_id, string $data) {
    // 投递异步任务，调用task()方法后，程序立即返回，继续向下执行代码。
    // onTask回调函数在Task进程池内被异步执行。执行完成后调用 finish() 方法返回结果。触发onFinish()回调事件。
    $task_id = $server->task($data);
    echo "分发一个异步任务: id={$task_id}\n";
    // send($fd, $data)：方法向客户端连接发送数据，参数就是$fd客户端标识符
    $server->send($fd, "分发一个异步任务: id={$task_id}\n");
});

// 处理异步任务
// 在 task_worker进程内被调用
// $task_id： 是任务id，由Swoole扩展内自动生成，用于区分不同的任务。
//          $task_id和$src_worker_id组合起来才是全局唯一的，不同的worker进程投递的任务ID可能会有相同
// $src_worker_id： 来自于哪个 worker进程
// $data： 是任务的内容
$serv->on('task', function (Server $server, int $task_id, int $src_worker_id, $data) {
    // 模拟处理费时的业务逻辑
    sleep(3);
    echo "一个新的异步任务：[id={$task_id}]\n";
    // 返回任务执行的结果
    $server->finish("{$data} -> OK");
});

// 处理异步任务的结果
// finish操作是可选的，也可以不返回任何结果
$serv->on('finish', function (Server $server, int $task_id, $data) {
    echo "异步任务-{$task_id} 执行完成：{$data}\n";
});

$serv->start();

