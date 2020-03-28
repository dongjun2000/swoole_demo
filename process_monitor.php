<?php

/**
 * 监控子进程
 *
 * pcntl_wait(&$status, $options)：回收子进程，防止僵尸进程。
 * pcntl_wtermsig($status): 返回导致子进程中断的信号编号。
 *                          $status参数是提供给成功调用 pcntl_wait()时的状态参数。
 */

echo "当前主进程ID = " . posix_getpid() . PHP_EOL;

$childs = [];

function fork()
{
    global $childs;

    $pid = pcntl_fork();

    // 父进程与子进程都会执行下面代码
    if ($pid == -1) {       // 错误处理：创建进程失败时返回-1
        die('fork failed.');
    } elseif ($pid == 0) {
        // 子进程
        echo "子进程的进程ID = " . posix_getpid() . PHP_EOL;

        // 保持子进程不退出
        while (true) {
            sleep(5);
        }
    } else {
        // 主进程
        // 将fork出来并返回的子进程id保存起来
        $childs[$pid] = $pid;
    }
}

// fork三个子进程
$count = 3;
for ($i = 0; $i < $count; $i++) {
    fork();
}

while (count($childs)) {
    // pcntl_wait() 返回退出的子进程号，发生错误时返回-1，如果提供了WNOHANG作为option并且没有可用子进程时返回0
    if (($exit_id = pcntl_wait($status)) > 0) {         // 阻塞，等待子进程中断，防止子进程成为僵尸进程
        echo "子进程{$exit_id}退出.\n";
        // pcntl_wtermsig()：返回导致子进程中断的信号编号。
        echo "中断子进程的信号值是: " . pcntl_wtermsig($status) . PHP_EOL;
        // 销毁退出的子进程
        unset($childs[$exit_id]);
    }

    // 保持开启3个子进程，当子进程小于3个时就开启一个子进程
    if (count($childs) < 3) {
        fork();
    }
}

echo "完成\n";