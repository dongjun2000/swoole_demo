<?php

/**
 * 协程：Go + Chan + Defer
 *
 * Swoole4为PHP语言提供了强大的CSP协程编程模式。底层提供了3个关键词，可以方便地实现各类功能。
 *      Swoole4提供的PHP协程语法借鉴自Golang
 *      PHP+Swoole协程可以与Golang很好地互补。
 *          Golang：静态语言，严谨强大性能好。
 *          PHP+Swoole：动态语言，灵活简单易用。
 *
 * Swoole协程提供了3个关键词：
 *      go：创建一个协程
 *      chan：创建一个通道
 *      defer：延迟任务，在协程退出时执行，先进后出
 *
 * 这3个功能底层全部为内存操作，没有任何 IO资源消耗。就像PHP的Array一样是非常廉价的。如果有需要就可以直接使用。
 * 这与Socket和file操作不同，后者需要向操作系统申请端口和文件描述符，读写可能会产生阻塞的IO等待。
 *
 * 使用 go() 函数可以让一个函数并发地去执行。在编程过程中，如果某一段逻辑可以并发执行，就可以将它放置到 go协程中执行。
 *
 * 顺序执行耗时等于所有任务执行耗时的总和： f1+f2+f3...
 * 并发执行耗时等于所有任务执行耗时的最大值： max(f1, f2, f3, ...)
 *
 * 有了go关键词之后，并发编程就简单多了。
 * 与此同时又带来了新问题，如果2个协程并发执行，另外一个协程，需要依赖这两个协程的执行结果，如果解决此问题？
 *      答案就是使用 Channel (通道)，在Swoole4协程中使用 new Chan 就可以创建一个通道。
 *
 * 通道可以理解为自带的协程调度的队列。它有两个接口 push 和 pop：
 *      push：向通道中写入内容，如果已满，它会进入等待状态，有空间时自动恢复。
 *      pop：从通道中读取内容，如果为空，它会进入等待状态，有数据时自动恢复。
 *
 * defer 延迟任务
 * 在延迟编程中，可能需要在协程退出时自动执行一些任务，做清理工作。
 * 类似于 php 的 register_shutdown_function，在Swoole4中可以使用 defer实现。
 */

// 顺序执行
// test1 和 test2 会顺序执行，需要3秒才能执行完成。
//function test1()
//{
//    sleep(1);
//    echo "b";
//}
//
//function test2()
//{
//    sleep(2);
//    echo "c";
//}
//
//test1();
//test2();


// 并发执行
// 使用go创建协程，可以让 test1 和 test2两个函数变成并发执行，这里只用2秒就执行完了。
//Swoole\Runtime::enableCoroutine();      // 将PHP提供的stream、sleep、pdo、mysqli、redis等功能从同步阻塞切换为协程的异步IO。
//
//go(function () {
//    sleep(1);
//    echo "b";
//});
//
//go(function () {
//    sleep(2);
//    echo "c";
//});


// 协程通信
//// 创建一个channel通道，对并发进行管理
//$chan = new Chan(2);
//
//// 协程1
//go(function () use ($chan) {
//    $result = [];
//    for ($i = 0; $i < 2; $i++) {
//        // 当channel通道中队列为空时，协程进行切换，当前协程进入等待状态。
//        // 当其他协程push数据到通道，当前协程pop取出数据，并继续向下执行。
//        $result += $chan->pop();
//    }
//    var_dump($result);
//});
//
//// 协程2
//go(function () use ($chan) {
//    $cli = new Swoole\Coroutine\Http\Client('www.mi360.cn', 443, true);
//    $cli->set(['timeout' => 10]);
//    $cli->setHeaders([
//        'Host'            => 'www.mi360.cn',
//        'User-Agent'      => 'Chrome/49.0.2587.3',
//        'Accept'          => 'text/html, application/xml',
//        'Accept-Encoding' => 'gzip',
//    ]);
//    $cli->get('/');
//    $chan->push(['www.mi360.cn' => $cli->statusCode]);
//});
//
//// 协程3
//go(function () use ($chan) {
//    $cli = new Swoole\Coroutine\Http\Client('www.qq.com', 443, true);
//    $cli->set(['timeout' => 10]);
//    $cli->setHeaders([
//        'Host'            => 'www.qq.com',
//        'User-Agent'      => 'Chrome/49.0.2587.3',
//        'Accept'          => 'text/html; application/xml',
//        'Accept-Encoding' => 'gzip',
//    ]);
//    $cli->get('/');
//    $chan->push(['www.qq.com' => $cli->statusCode]);
//});


// 延迟任务
Swoole\Runtime::enableCoroutine();

go(function () {
    echo "a";
    defer(function () {
        echo "~a";
    });
    echo "b";
    defer(function () {
        echo "~b";
    });
    sleep(1);
    echo "c";
});
