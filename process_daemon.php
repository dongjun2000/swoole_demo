<?php

/**
 * 守护进程化
 *
 * 在命令行下启动的服务程序会被当前会话终端所控制，php服务成了终端程序的一个子进程。
 * 如果关闭了终端，这个命令行程序也会随之关闭。
 *
 * 要使php服务不受终端影响而常驻系统，就需要将它变成守护进程。
 *
 * 守护进程就是 Daemon程序，是一种在系统后台执行的程序，
 * 它独立于控制终端并且执行一些 周期任务 或 触发事件，通常被命名为"d"字母结尾，如常见的httpd、syslogd、systemd 和 dockerd等。
 *
 * posix_setsid()： 让当前进程成为新的会话组长和进程组长，
 *                  返回会话ID，如果错误则返回-1。
 */

// 定义守护进程函数
function daemon()
{
    $pid = pcntl_fork();

    if ($pid == -1) {
        die('fork failed');
    } else if ($pid > 0) {
        // 父进程
        exit;       // 父进程退出了
    } else {
        // 子进程
        // 让子进程成为新的会话组长和进程组长
        if (($sid = posix_setsid()) <= 0) {
            die("设置 sid 失败.\n");
        }

        // 子进程默认继承父进程的工作目录，最好是变更到根目录，否则会影响文件系统的卸载
        if (chdir('/') === false) {
            die("将工作目录切换到根目录失败.\n");
        }

        // 子进程默认继承父进程的umask（文件权限掩码），重设为0（完全控制），以免影响程序读写文件
        umask(0);

        // 关闭标准输入/输出/错误接口
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
    }
}

function fork()
{
    global $childs;

    $pid = pcntl_fork();

    if ($pid == -1) {
        die('fork failed.');
    } elseif ($pid == 0) {
        // 子进程
        while (true) {
            sleep(5);
        }
    } else {
        // 父进程
        $childs[$pid] = $pid;
    }
}

daemon();

$childs = [];

$count = 3;

for ($i = 0; $i < $count; $i++) {
    fork();
}

while (count($childs)) {
    // pcntl_wait() 返回退出的子进程号，发生错误时返回-1，如果提供了WNOHANG作为option并且没有可用子进程时返回0
    if (($exit_id = pcntl_wait($status)) > 0) {     // 阻塞，等待子进程中断，防止子进程成为僵尸进程
        // 销毁退出的子进程
        unset($childs[$exit_id]);
    }

    // 保持开启3个子进程，当子进程小于3个时就开启一个子进程
    if (count($childs) > 3) {
        fork();
    }
}