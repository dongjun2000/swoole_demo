<?php

/**
 * 任务控制信号
 *
 * pcntl_signal() 安装一个信号处理器
 * pcntl_kill()： 发送一个信号
 */

// 守护进程化
function daemon()
{
    $pid = pcntl_fork();

    if ($pid == -1) {
        die('fork failed.');
    } elseif ($pid > 0) {
        // 父进程
        exit;       // 主进程退出了，子进程成为孤儿子进程，由系统的1号进程(init/systemd)接管。
    } else {
        // 子进程
        if (($sid = posix_setsid()) <= 0) {
            die("设置新的进程会话组长失败.\n");
        }
        if (chdir('/') == false) {
            die("将工作目录切换到根目录失败.\n");
        }
        // 将文件权限掩码重设为 0
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
    } else if ($pid > 0) {
        // 父进程
        $childs[$pid] = $pid;
    } else {
        // 子进程
        // pcntl_signal()：安装一个信号处理器；  SIGTERM：终止， SIG_IGN：忽略信号处理程序
        pcntl_signal(SIGTERM, SIG_IGN, false);
        while (true) {
            sleep(5);
        }
    }
}

// $_SERVER['argv']： 获取传递给该脚本的参数的数组。
// ??：null合并运算符，如果变量存在且不为NULL，它就会返回自身的值，否则返回它的第二个操作数。
$cmd = $_SERVER['argv'][1] ?? '';

switch ($cmd) {
    case 'start':       // 启动

        if (file_exists('/tmp/master_pid')) {
            die('程序已经运行\n');
        }
        break;
    case 'reload':      // 重新载入子进程
        // 读取主进程ID
        $master_pid = file_get_contents('/tmp/master_pid');
        // 获取主进程下的所有子进程ID
        exec("ps --ppid {$master_pid} | awk '/[0-9]/{print $1}' | xargs", $output, $status);
        if ($status == 0) {
            $childs = explode(' ', current($output));
            foreach ($childs as $id) {
                // 杀死所有子进程
                // posix_kill()：向进程发送信号；  SIGKILL（杀死）：相当于linux命令中的 `kill -9 $id`
                posix_kill($id, SIGKILL);
            }
        }

        // 退出当前进程
        exit;
        break;
    case 'stop':        // 停止所有
        // 读取主进程ID
        $master_pid = file_get_contents('/tmp/master_pid');
        // 获取主进程下的所有子进程ID
        exec("ps --ppid {$master_pid} | awk '/[0-9]/{print $1}' | xargs", $output, $status);
        // 杀死主进程ID
        posix_kill($master_pid, SIGKILL);
        if ($status == 0) {
            $childs = explode(' ', current($output));
            foreach($childs as $id) {
                posix_kill($id, SIGKILL);
            }
        }

        // 删除主进程ID存储文件
        while (true) {
            if (!posix_kill($master_pid, 0)) {
                @unlink('/tmp/master_pid');
                break;
            }
        }

        // 退出当前进程
        exit;
        break;
    default:
        die("请重新输入命令!\n");
        break;
}

// 守护进程
daemon();

// 定义一个数组变量，用于存储子进程号
$childs = [];

// 开启子进程数量
$count = 3;

// 获取主进程pid
$master_pid = posix_getpid();
file_put_contents('/tmp/master_pid', $master_pid);

// fork子进程
for ($i = 0; $i < $count; $i++) {
    fork();
}

// 监控子进程
while (count($childs)) {
    if (($exit_id = pcntl_wait($status)) > 0) {
        unset($childs[$exit_id]);
    }

    if (count($childs) < 3) {
        fork();
    }
}
