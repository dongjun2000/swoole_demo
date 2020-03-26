<?php

/**
 * 多进程共享数据
 *
 * 由于 PHP 语言不支持多线程，因此 Swoole 使用多进程模式。
 * 在多进程模式下存在进程内存隔离，在工作进程内修改 global全局变量 和 超全局变量 时，在其他进程是无效的。
 *
 * 注意：当设置 worker_num=1 时，只有一个worker进程在运行，也就不存在进程隔离，可以使用全局变量保持数据。
 *
 * 对应的解决方案就是使用外部存储服务：
 *      数据库，如：MySQL、MongoDB
 *      缓冲服务器，如：Redis、Memcache
 *      磁盘文件，多进程并发读写时需要加锁
 *
 * 普通的数据库和磁盘文件操作，存在较多IO等待时间，因此推荐使用：
 *      Redis内存数据库，读写速度非常快
 *      /dev/shm内存文件系统，读写操作全部在内存中完成，无IO消耗，性能极高
 *
 * 除了使用存储之外，还可以使用 共享内存 来保存数据
 *
 * PHP提供了多套共享内存的扩展，但实际上真正在实际项目中可用的并不多。
 *
 *      shm扩展（不推荐使用）
 *          提供了 shm_put_var / shm_get_var 共享内存读写方法。
 *          但底层实现使用链表结构，在保存大量数值时时间复杂度为 O(N)，性能非常差。
 *          并且读写数据没有加锁，存在数据同步问题，需要使用者自行加锁。
 *
 *      shmop扩展（不推荐使用）
 *          提供了 shmop_read / shmop_write 共享内存读写方法。
 *          仅提供了基础的共享内存操作指令，并未提供数据结构和封装。
 *          不适合普通开发者使用。
 *
 *      apcu扩展
 *          提供了 apc_fetch / apc_store 可以使用 Key-Value 方式访问。
 *          APC 扩张总体上是可以用于实际项目的，缺点是锁的粒度较粗，在大量并发读写操作时锁的碰撞较为密集。
 *
 *      Swoole\Table（推荐使用）
 *          Swoole官方提供的共享内存读写工具，提供了 Key-Value 操作方式，使用非常简单。
 *          底层使用自旋锁实现，在大量并发读写操作时性能依然非常强劲。
 *          Table仍然存在两个缺点，使用时需要根据实际情况来选择。
 *              1.提前申请内存，Table在使用前就需要分配好内存，可能会占用较多内存。
 *              2.无法动态扩容，Table内存管理是静态的，不支持动态申请新内存，因此一个Table在设置好行数并创建之后，使用时不能超过限制
 *
 */

$serv = new Swoole\Server('0.0.0.0', 9501);

$serv->set([
    'worker_num' => 2,      // 开启2个worker进程
]);

/**
 * $fds 虽然是全局变量，但只在当前的进程内有效。
 * Swoole服务器底层会创建多个Worker进程(进程内存隔离)，所以在var_dump($fds)打印出来的值，只有部分连接的fd。
 */
$fds = [];
$serv->on('connect', function (Swoole\Server $server, int $fd) {
    echo "connection open: {$fd}\n";
    global $fds;
    $fds[] = $fd;
    var_dump($fds);
});

$serv->on('receive', function (Swoole\Server $server, int $fd, int $reactor_id, string $data) {

});

$serv->on('close', function (Swoole\Server $server, int $fd) {

});

$serv->start();



