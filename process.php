<?php

/**
 * Swoole多进程： 创建子进程
 * 
 * Swoole\Process：
 *      Swoole 的进程管理模块，可以作为 PHP Pcntl的易用版本。
 * 
 *      与 pcntl 相比的几点优势：
 *          1.集成了进程间通信的 API。
 *          2.支持重定向标准输入输出。
 *          3.面向对象的操作 API 易于使用。
 * 
 * 创建子进程：
 *      Swoole\Process::__construct(callable $function, bool $redirect_stdin_stdout=false, int $pipe_type=SOCK_DGRAM, bool $enable_coroutine=false);
 *      参数：
 *          $function                   子进程创建成功后要执行的函数。
 *          $redirect_stdin_stdout      是否重定向子进程的标准输入和输出。
 *          $pipe_type                  管道类型，启用第二个参数后，值将被忽略 强制为1。
 *          $enable_coroutine           默认为 false，(4.3.0)开启后可以在 callback 中使用协程API。
 * 
 * 启动进程：
 *      Process->start()： int|bool
 *      创建成功返回子进程的 PID，创建失败返回 false。可以使用 swoole_errno()、swoole_strerror(int $errno) 获取当前的错误码和错误信息。
 *      
 *      $process->pid   子进程的 PID
 *      $process->pipe  管道的文件描述符
 *  
 * 修改进程名称：
 *      Process->name(`php worker`)
 *      可以修改主进程名，修改子进程名是在 start 之后的子进程回调函数中使用；此方法是 swoole_set_process_name() 的别名。
 * 
 * 执行一个外部程序：
 *      Process->exec(string $execfile, array $args): bool
 *      执行成功后，当前进程的代码段将会被新程序替换。
 *      参数：
 *          $execfile   可执行文件的绝对路径，如 /bin/echo
 *          $args       参数列表，如 ['aaa']
 * 
 * 退出子进程：
 *      Process->exit(int $status = 0): int
 *      参数：
 *          $status     退出进程的状态码，如果为 0 表示正常结束，会继续执行清理工作。
 *                      包括： PHP 的 shutdown_function；对象析构 __destruct；其他扩展的 RSHUTDOWN 函数。
 *                      如果 $status 不为 0，表示异常退出，会立即终止进程，不再执行清理工作。
 * 
 * 设置 CPU 亲和性：
 *      Process::setAffinity(array $cpu_set)
 *      可以将进程绑定到特定的 CPU 核上，作用是让进程只在某几个 CPU 核上运行，让出某些 CPU 资源执行更重要的程序。
 *      接受一个数组绑定哪些 CPU 核，如 [0, 2, 3] 表示绑定 CPU0、CPU2、CPU3。
 *      
 *      使用 swoole_cpu_num() 可以得到当前服务器的 CPU 核数。
 * 
 */

 // 开 4 个子进程
for ($i = 0; $i < 5; $i++) {
    $p = new Swoole\Process(function (Swoole\Process $p) {
        $p->name('process child');
        sleep(5);
    });

    $p->name('process master');
    $p->start();
}

sleep(10);