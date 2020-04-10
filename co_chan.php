<?php

/**
 * 子协程与通道实现并发请求
 * 
 *      1.主协程内创建一个 chan。
 *      2.主协程内创建 2 个子协程分别进行 IO 请求，子协程使用 use 应用 chan。
 *      3.主协程循环调用 $chan->pop()，等待子协程完成任务，进入挂起状态。
 *      4.并发的两个子协程，完成请求的 调用 $chan->push() 将数据推送给主协程。
 *      5.子协程完成请求后退出，主协程从挂起状态中恢复，继续向下执行。
 * 
 * 更多文档@doc：
 *      https://wiki.swoole.com/wiki/page/947.html
 */

$serv = new Swoole\Http\Server('0.0.0.0', 9501, SWOOLE_BASE);

$serv->on('request', function ($req, $resp) {
    $chan = new Chan(2);

    go(function () use ($chan) {
        $cli = new Swoole\Coroutine\Http\Client('www.mi360.cn', 443, true);
        $cli->set(['timeout' => 3]);
        $cli->setHeaders([
            'Host'              => 'www.mi360.cn',
            'User-Agent'        => 'Chrome/49.0.2587.3',
            'Accept'            => 'text/html;application/xml',
            'Accept-Encoding'   => 'gzip',
        ]);
        $cli->get('/');
        $chan->push(['www.mi360.cn' => $cli->statusCode]);
    });

    go(function () use ($chan) {
        $cli = new Swoole\Coroutine\Http\Client('www.qq.com', 443, true);
        $cli->set(['timeout' => 3]);
        $cli->setHeaders([
            'Host'              => 'www.qq.com',
            'User-Agent'        => 'Chrome/49.0.2587.3',
            'Accept'            => 'text/html; application/xml',
            'Accept-Encoding'   => 'gzip',
        ]);
        $cli->get('/');
        $chan->push(['www.qq.com' => $cli->statusCode]);
    });

    $result = [];
    for ($i = 0; $i < 2; $i++) {
        $result += $chan->pop();
    }
    $resp->end(json_encode($result));
});

$serv->start();
