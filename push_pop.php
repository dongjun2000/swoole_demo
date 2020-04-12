<?php

/**
 * Swoole多进程： 消息队列通信
 * 
 * 启用消息队列作为进程间通信：
 *      Process->useQueue(int $msgKey = 0, int $mode = 2, int $capacity = 8192): bool
 *      参数：
 *          $msgKey     是消息队列的 key， 默认会使用 ftok(__FILE__, 1) 作为 KEY。
 *          $mode       通信模式，默认为 2，表示争抢模式， 所有子进程都会从队列中取数据。
 *          $capacity   单个消息长度，长度受限于操作系统内核参数的限制，默认为 8192，最大不超过 65535。
 * 
 * 查看消息队列状态：
 *      Process->statQueue(): array
 *          返回的数组中包括 2 项， queue_num 队列中的任务数量， queue_bytes 队列数据的总字节数。
 * 
 * 删除队列：
 *      Process->freeQueue()
 *          此方法与 useQueue 成对使用， useQueue 创建, freeQueue 销毁， 销毁队列后， 队列中的数据会被清空。
 *          如果只调用 useQueue，未调用 freeQueue， 在程序结束时并不会清除数据，重新运行程序可以继续读取上次运行时留下的数据。
 * 
 * 投递数据到消息队列中：
 *      Process->push(string $data): bool
 *          $data 要投递的数据，长度受限于操作系统内核参数的限制。
 *          默认阻塞模式，如果队列已满， push 方法会阻塞等待。
 *          非阻塞模式下，如果队列已满， push 方法会立即返回 false。
 * 
 * 从队列中提取数据：
 *      Process->pop(int $maxsize = 8192): string|bool
 *          $maxsize 表示获取数据的最大尺寸，默认为 8192
 *          操作成功会返回提取到的数据内容，失败返回 false
 *          默认阻塞模式下，如果队列中没有数据，pop 方法会阻塞等待
 *          非阻塞模式下，如果队列中没有数据，pop 方法会立即返回 false，并设置错误码为 ENOMSG
 * 
 */

for ($i = 0; $i < 5; $i++) {
    $p = new Swoole\Process(function (Swoole\Process $p) {
        $p->name('process child');

        while (true) {
            // 阻塞读
            $msg = $p->pop();

            if ($msg === false) {
                break;
            }

            echo "\n $p->pid received msg {$msg} \n";
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
    //单批次
    echo "\n ======================== \n";
    foreach (['a', 'b', 'c', 'd', 'e'] as $message) {
        $p->push($message);
    }
    sleep(2);
}

sleep(10);
