<?php

/**
 * Swoole多进程： 管道数据读写
 * 
 * 向管道内写入数据：
 *      Process->write(string $data): int|bool
 *          1.在子进程内调用 write，父进程可以调用 read 接收此数据
 *          2.在父进程内调用 write，子进程可以调用 read 接收此数据
 * 
 * 从管道中读取数据：
 *      Process->read(int $buffer_size = 8192): string
 *          1.$buffer_size 是缓冲区的大小，默认为 8192，最大不超过 64K。
 *          2.管道类型为 DGRAM 数据时， read 可以读取完整的一个数据包。
 *          3.管道类型为 STREAM 时， read 是流式的，需要自行处理完整性问题。
 *          4.读取成功返回二进制数据字符串，读取失败返回 false。
 *  
 * 关闭创建好的管道：
 *      Process->close(int $which = 0)
 *          1.$which 指定关闭哪一个管道。
 *          2.默认为 0 表示同时关闭读和写，1：关闭读，2：关闭写。
 * 
 * 设置管道读写操作的超时时间：
 *      Process->setTimeout(double $timeout): bool
 *          1.$timeout 单位为秒，支持浮点型，设置成功返回 true，设置失败返回 false。
 *          2.设置成功后，调用 recv 和 write 在规定时间内未读取或写入成功，将返回 false。
 * 
 * 设置管道是否为阻塞模式：
 *      Process->setBlocking(bool $blocking = true)
 *          1.$blocking 默认为 true 同步阻塞，设置为 false 时管道为非阻塞模式。
 * 
 * 将管道导出为 Coroutine\Socket 对象
 *      Process->exportSocket()： Swoole\Coroutine\Socket
 *          1.多次调用此方法，返回的对象是同一个。
 *          2.进程未创建管道，操作失败，返回 false。
 */

$p = new Swoole\Process(function (Swoole\Process $p) {
    $p->name('process child');

    echo "\n child send start\n";
    $p->write('from child, hello');
    echo "\n child send end\n";

    echo "\n received from master: " . $p->read() . PHP_EOL;
});

$p->name('process master');
$p->start();

echo "\n received from child: " . $p->read() . PHP_EOL;

$p->write('form master, hello ~');

sleep(10);