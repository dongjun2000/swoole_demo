<?php

/**
 * 随机函数的影响
 *
 * rand()、srand()、shuffle()、array_rand()
 *
 * 主进程使用过随机数发生器，子进程内 rand()函数返回的结果是相同的。
 *
 * 处理方式：
 *      1.主进程内不使用随机数函数。
 *      2.在子进程内使用 srand() 重新播种。
 */

$workerNum = 3;

// 主进程使用了会生成随机数种子的函数
srand();
//rand(1, 10);
//$arr = [1, 2, 3, 4];
//shuffle($arr);
//array_rand($arr);

// 开启多个子进程
for ($i = 0; $i < $workerNum; $i++) {
    $process = new Swoole\Process(function (Swoole\Process $process) {
        // 子进程必须重新生成随机数种子
        // srand();

        echo PHP_EOL . rand(0, 10) . PHP_EOL;
        $process->exit();
    });
    $process->start();
}

sleep(1);
