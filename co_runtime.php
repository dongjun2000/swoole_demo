<?php

/**
 * 网络客户端一键协程
 * 
 * Swoole\Runtime：
 *      1.Swoole-4.1.0 版本新增，在运行时动态将 PHP Stream 实现的扩展、网络客户端代码协程化。
 *      2.底层替换了 ZendVM、Stream 的函数指针，所有使用 Stream 进行 socket 操作均变成协程调度的异步 IO。
 * 
 * 开启方式：
 *      Swoole\Runtime::enableCoroutine(bool $enable = true, int $flags = SWOOLE_HOOK_ALL);
 *      参数：
 *          $enable： 打开或关闭协程，
 *          $flags： 选择要 Hook 的类型，仅在 $enable = true 时有效，默认全选。
 *                  @支持的选项 https://wiki.swoole.com/wiki/page/993.html
 * 
 * 可用场合：
 *      1.redis 扩展
 *      2.使用 mysqlnd 模式的 PDO、MySQLi 扩展
 *      3.soap 扩展
 *      4.stream_socket_client、stream_socket_server、stream_select ( 4.3.2以上 )
 *      5.fsockopen
 *      6.file_get_contents、fopen、fread/fgets、fwrite/fputs、unlink、mkdir/rmdir
 *      7.sleep、usleep
 * 
 * 使用位置：
 *      1.调用后当前进程全局有效，一般放在整个项目最开头，只在 Swoole 协程中会被切换为协程模式，
 *      2.在非协程中依然是同步阻塞的，不影响 PHP 原生环境使用。
 *          （Swoole-4.4.0 中不再自动兼容协程内外环境，一旦开启，则一切阻塞操作必须在协程内调用）
 *      3.不建议放在 onRequest 等回调中，会多次调用造成不必要的开销。
 */


Swoole\Runtime::enableCoroutine(true);

Swoole\Coroutine::set([
    'max_coroutine' => 2000,
]);

for ($i = 0; $i < 1000; $i++) {
    go (function () {
        echo 'A';
        sleep(5);
        echo 'B';
    });
}
