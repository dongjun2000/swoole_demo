<?php

/**
 * 使用协程客户端
 *
 * 在最新的 Swoole 4.x版本中，协程 取代 了异步回调，作为我们推荐使用的编程方式。
 *
 * 协程解决了异步回调编程困难的问题。
 * 使用协程可以以传统同步编程的方法编写代码，底层自动切换为异步IO，既保证了编程的简单性，又可借助异步IO，提升系统的并发能力。
 *
 * 使用协程客户端，代码编写与同步阻塞模式的程序完全一致。但是底层自动进行了协程切换处理，变为异步IO。
 *  因此：
 *      服务器可以应对大量并发，每个请求都会创建一个新的协程，执行对应的代码
 *      某些请求处理较慢时，只会引起这一个请求被挂起，不影响其他请求的处理。
 *
 * Swoole4提供了丰富的协程组件，如：Redis、TCP/UDP/Unix客户端、Http/WebSocket/Http2客户端，
 * 使用这些组件可以很方便地实现高性能的并发编程。
 *
 * 协程非常适合编写：
 *      1.高并发服务，如秒杀系统、高性能API接口、RPC服务器，使用协程模式，服务的容错率会大大增加，某些接口出现故障时，不会导致整个服务崩溃。
 *      2.爬虫，可实现非常巨大的并发能力，即使是非常慢速的网络环境，也可以高效的利用带宽。
 *      3.即时通信服务，如：IM聊天、游戏服务器、物联网、消息服务器等等，可以确保消息通信完全无阻塞，每个消息包均可即时地被处理。
 */

$http = new Swoole\Http\Server('0.0.0.0', 9501);

$http->on('request', function($request, $response) {
    $db = new Swoole\Coroutine\MySQL();
    $db->connect([
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'test',
    ]);
    $data = $db->query('select * from test_table');
    $response->end(json_encode($data));
});

$http->start();