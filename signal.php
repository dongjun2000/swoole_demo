<?php

/**
 * Swoole多进程： 信号监听
 * 
 * 设置异步信号监听：
 *      Process::signal(int $signo, callable $callback): bool
 *          1.此方法基于 signalfd 和 eventloop， 是异步 IO， 不能用于同步程序中。
 *          2.同步阻塞的程序可以使用 pcntl 扩展提供的 pcntl_signal。
 *          3.$callback 如果为 null， 表示移除信号监听。
 *          4.如果已设置信号回调函数，重新设置时会覆盖历史设置。
 * 
 * 回收结束运行的子进程：
 *      Process::wait(bool $blocking = true): array|bool
 *          1.子进程结束必须要执行 wait 进程回收，否则子进程会变成僵尸进程。
 *          2.$blocking 参数可以指定是否阻塞等待，默认为阻塞。
 *          3.操作成功会返回一个数组包含子进程的 PID、退出状态码、被哪种信号 kill,
 *              如：['pid' => 15001, 'code' => 0, 'signal' => 15]，失败返回 false。
 * 
 * 向指定 PID 进程发送信号：
 *      Process::kill($pid, $signo = SIGTERM): bool
 *          1.默认的信号为 SIGTERM， 表示终止进程
 *          2.$signo = 0 可以检测进程是否存在， 不会发送信号
 * 
 * 高精度定时器：
 *      Process:alarm(int $interval_usec, int $type = 0): bool
 *          1.定时器会触发信号， 需要与 Process::signal 或 pcntl_signal 配合使用。
 *          2.$interval_usec 定时器间隔时间，单位为微妙，如果为负数表示清除定时器。
 *          3.$type 定时器类型：
 *              0： 表示为真实时间，触发 SIGALAM 信号
 *              1： 表示用户态 CPU 时间，触发 SIGVTALAM 信号
 *              2： 表示用户态 + 内核态时间，触发 SIGPROF 信号
 */

for ($i = 0; $i < 5; $i++) {
    $p = new Swoole\Process(function (Swoole\Process $p) {
        sleep(rand(2, 6));
    });

    $p->start();
}

Swoole\Process::signal(SIGCHLD, function ($signo) {
    echo "\n $signo \n";

    while ($ret = Swoole\Process::wait(false)) {
        print_r($ret);
    }
});

echo "end";
