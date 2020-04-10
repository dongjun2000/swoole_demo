<?php

/**
 * 
 * 协程：并发 shell_exec
 *
 * 在 PHP 程序中经常需要用 shell_exec 执行一些 shell 命令，而普通的 shell_exec 是阻塞的，如果命令执行时间过长，那可能会导致进程完全卡住。
 * 在 Swoole4 协程环境下可以用 Co::exec 并发地执行很多命令。
 *
 * Swoole 提供的协程是并发编程的利器。
 * 在工作中很多地方都可以使用协程，实现并发程序，大大提升程序性能。
 */

/**  php原生阻塞代码
$ time php coroutine_exec.php

real    0m50.064s
user    0m0.028s
sys     0m0.030s
 */
//$c = 10;
//while ($c--) {
//    // 这里使用 sleep 5来模拟一个很长的命令
//    shell_exec('sleep 5');
//}

/** Swoole 协程代码
$ time php coroutine_exec.php

real    0m5.054s
user    0m0.026s
sys     0m0.023s
 */
$c = 10;
while($c--) {
    go(function () {
        // 这里使用 sleep5来模拟一个很长的命令
        co::exec('sleep 5');
    });
}