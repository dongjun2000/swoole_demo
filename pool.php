<?php

/**
 * Swoole多进程： 进程池
 * 
 * Swoole\Process\Pool：
 *      进程池，基于 Server 的 Manager 模块实现，可管理多个工作进程。
 *      相比 Process 实现多进程， Process\Pool 更加简单，封装层次更高，开发者无需编写过多代码即可实现进程管理功能。
 * 
 *      SWOOLE_IPC_MSGQUEUE     系统消息队列通信
 *      SWOOLE_IPC_SOCKET       SOCKET 通信
 *      SWOOLE_IPC_UNIXSOCK     Unix Socket 通信
 * 
 * 创建进程池：
 *      Process\Pool->__construct(int $worker_num, int $ipc_type = 0, int $msgqueue_key = 0, bool $enable_coroutine = false)
 *          $worker_num     指定工作进程的数量
 *          $ipc_type       进程间通信的模式，默认为 0 表示不使用任何进程间通信特性
 *          $msgqueue_key   使用消息队列通信模式时，可设置消息队列的键
 *          $enable_coroutine   (4.4版本)启用协程
 * 
 * 设置进程池回调函数：
 *      Process\Pool->on(string $event, callable $function)
 * 
 *          onWorkerStart(Process\Pool $pool, int $workerId)        子进程启动（必须设置）
 *          onWorkerStop(Process\Pool $pool, int $workerId)         子进程结束
 *          onMessage(Process\Pool $pool, string $data)             消息接收
 * 
 * 监听SOCKET：
 *      Process\Pool->listen(string $host, int $port = 0, int $backlog = 2048): bool
 *          $host       监听的地址，支持 TCP、UnixSocket 类型
 *          $port       监听的端口，TCP 模式下指定
 *          $backlog    监听的队列长度
 * 
 * 向对端写入数据：
 *      Process\Pool->write(string $data)
 *          $data       写入的数据内容
 *          多次调用 write，底层会在 onMessage 函数退出后将全部数据写入 socket 中，并close连接。发送操作时同步阻塞的。
 *              内存操作，无 IO消耗。
 * 
 * 启动工作进程：
 *      Process\Pool->start(): bool
 *          启动成功，当前进程进入 wait 装填，管理工作进程
 *          启动失败，返回 false，可使用 swoole_errno 获取错误码
 * 
 * 获取当前工作进程对象：
 *      Process\Pool->getProcess($worker_id): Process
 *          $worker_id      可选参数，指定获取 worker，默认当前 worker
 *          必须在 start 之后，在工作进程的 onWorkerStart 或其他回调函数中调用，
 *              返回的 Process 对象是单例模式，在工作进程中重复调用 getProcess() 将返回一个对象。
 */

$pool = new Swoole\Process\Pool(5);

$pool->on('workerStart', function (Swoole\Process\Pool $pool, int $worker_id) {
    echo "\n $worker_id \n";
    sleep(rand(2, 6));
});

$pool->start();
