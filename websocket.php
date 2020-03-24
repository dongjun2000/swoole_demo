<?php

/**
 * 创建WebSocket服务器
 *
 * WebSocket服务器是建立在Http服务器之上的长连接服务器(允许服务器主动发送消息给客户端)，客户端首先会发送一个Http的请求与服务器进行握手。
 * 握手成功后会触发 onOpen 事件，表示连接已就绪，onOpen函数中可以得到 $request 对象，包含了Http握手的相关信息，如：GET参数、Cookie、Http头信息等。
 *
 * 建立连接后客户端与服务器端就可以双向通信了：
 *      客户端向服务器端发送信息时，服务器端触发 onMessage 事件回调
 *      服务器端可以调用 $server->push() 向某个客户端(使用$fd标识符)发送消息
 *      服务器端可以设置 onHandShake 事件回调来手工处理 WebSocket握手
 *      Swoole\Http\Server 是 Swoole\Server 的子类，内置了 Http 的支持
 *      Swoole\WebSocket\Server 是 Swoole\Http\Server 的子类，内置了 WebSocket 的支持
 *
 * onRequest回调：
 *      设置了 onRequest回调，WebSocket\Server也可以同时作为http服务器
 *      未设置 onRequest回调，WebSocket\Server收到http请求后会返回 http400错误页面
 *      如果想通过接收 http 触发所有webSocket的推送，需要注意作用域的问题，
 *          面向过程请使用 global对WebSocket\Server引用，
 *          面向对象可以把 WebSocket\Server设置成一个成员属性。
 *
 */

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

// 创建 WebSocket服务器对象，监听ip:port
$ws = new Server('0.0.0.0', 9502);

// 监听WebSocket连接打开事件
// $request 对象，包含了Http握手的相关信息，如：GET参数、Cookie、Http头信息等。
$ws->on('open', function (Server $ws, $request) {
    // var_dump("建立连接：", $request);
    var_dump("建立连接：", $request->fd, $request->get, $request->server);
    // 服务器端可以调用 $server->push() 向某个客户端(使用$fd标识符)发送消息
    $ws->push($request->fd, "Hello, welcome\n");
});

// 监听WebSocket消息事件
// 客户端向服务器端发送信息时，服务器端触发 onMessage 事件回调
$ws->on('message', function (Server $ws, Frame $frame) {
    // var_dump("接收数据：", $frame);
    var_dump("接收数据：", $frame->data);
    $ws->push($frame->fd, "Server：{$frame->data}");
});

// 监听WebSocket连接关闭事件
$ws->on('close', function (Server $ws, $fd) {
    var_dump("连接关闭：", $fd);
});

// 设置了 onRequest回调，WebSocket\Server也可以同时作为http服务器
// 给所有连接上来的ws客户端推送消息
$ws->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    // Chrome请求两次问题：使用Chrome访问服务器，会产生额外的一次请求， /favicon.ico
    if ($request->server['request_uri'] == '/favicon.ico' || $request->server['path_info'] == '/favicon.ico') {
        // status()：设置状态码为404
        $response->status(404);
        // 直接结束本次请求
        $response->end();
    }

    // 使用 global对外部变量$ws引用
    global $ws;
    // $server->connections 遍历所有 WebSocket连接用户的fd，给所有用户推送
    foreach ($ws->connections as $fd) {
        // 需要先判断是否是正确的 WebSocket连接，否则有可能会 push失败
        if ($ws->isEstablished($fd)) {
            $ws->push($fd, $request->get['message']);
        }
    }
    var_dump($request->get['message']);
    $response->header('Content-Type', 'text/html; charset=utf-8');
    $response->end($request->get['message']);
});

// 启动ws服务器
$ws->start();
