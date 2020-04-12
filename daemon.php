<?php

/**
 * Swoole多进程： 守护进程化
 * 
 * 使当前进程蜕变为一个守护进程：
 *      Process::daemon(bool $nochdir = false, bool $noclose = false)
 *          $nochdir 为 true 表示不要切换当前目录到根目录
 *          $noclose 为 true 表示不要关闭标准输入输出文件描述符
 * 
 *          蜕变为守护进程时，进程 PID 将发生变化，可以使用 getmypid() 获取当前 PID。
 * 
 */

// 只需要加这一行即可变为守护进程程序
Swoole\Process::daemon();

for ($i = 0; $i < 5; $i++) {
    $p = new Swoole\Process(function (Swoole\Process $p) {
        $p->name('process child');

        while (true) {
            // 阻塞读
            $msg = $p->pop();

            if ($msg === false) {
                break;
            }

            // echo "\n $p->pid received msg {$msg} \n";
        }
    });

    if ($p->useQueue() === false) {
        throw new \Exception('use queue failed');
    }

    $p->name('process master');
    $p->start();
}

sleep(1);

while (true) {
    // 单批次
    // echo "\n =========== \n";
    foreach (['a', 'b', 'c', 'd', 'e'] as $message) {
        $p->push($message);
    }
    sleep(2);
}

sleep(10);
