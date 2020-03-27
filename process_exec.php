<?php

/**
 * 用子进程执行外部命令
 */

echo "当前主进程ID = " . posix_getpid() . PHP_EOL;

$pid = pcntl_fork();

if ($pid == 0) {
    // 子进程
    echo "子进程ID = " . posix_getpid() . PHP_EOL;
    sleep(2);
    // exec('sh ./test.sh A B');
    pcntl_exec("./test.sh", ['AA', 'BB']);
    echo "我将要退出啦\n";
} elseif ($pid > 0) {
    // 主进程
    if ($exit_id = pcntl_waitpid($pid, $status, WUNTRACED)) {
        echo "子进程({$exit_id})退出\n";
    }
    echo "父进程ID = " . posix_getpid() . PHP_EOL;
} else {
    die('fork failed.');
}