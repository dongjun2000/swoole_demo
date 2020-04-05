<?php

/**
 * 创建WebSocket服务器
 *
 * Swoole\WebSocket\Server 继承自 Swoole\Http\Server，是实现了 WebSocket 协议的服务器，父类提供的 API 都可使用，
 * 通过几行 PHP 代码就可以写出一个异步非阻塞多进程的 WebSocket 服务器。
 *
 * 有哪些 WebSocket 客户端：
 *      1.浏览器内置的 JavaScript WebSocket 客户端。
 *      2.实现了 WebSocket 协议解析的程序都可以作为客户端。
 *      3.非 WebSocket 客户端不能与 WebSocket 服务器通信。
 *
 * 回调函数：
 *      除了接收 Swoole\Server 和 Swoole\Http\Server 基类的回调函数外，额外增加三个回调函数设置。
 *      onOpen      可选，客户端与服务器建立连接并完成握手时回调此函数
 *      onHandShake 可选，建立连接后进行握手，不使用内置 handshake 时候设置
 *      onMessage   必选，服务器收到客户端数据帧时回调此函数
 *                      参数一是 Server 对象，
 *                      参数二是 Swoole\WebSocket\Frame 对象
 *                      Frame@doc：https://wiki.swoole.com/wiki/page/987.html
 *
 * 方法列表：
 *      push            向 WebSocket 客户端连接发送数据
 *      exist           判断 WebSocket 客户端是否存在
 *      pack            打包 WebSocket 消息
 *      unpack          解析 WebSocket 数据帧
 *      disconnect      主动向 WebSocket 客户端发送关闭帧并关闭连接
 *      isEstablished   检查连接是否为有效的 WebSocket 客户端连接，
 *                          exist 仅判断是否为 TCP 连接，无法判断是否为已完成握手的 WebSocket 连接。
 *
 * 预定义常量：
 *      WebSocket 数据帧类型：
 *          WEBSOCKET_OPCODE_TEXT = 0x1     UTF-8 文本字符串数据
 *          WEBSOCKET_OPCODE_BINARY = 0x2   二进制数据
 *          WEBSOCKET_OPCODE_PING = 0x9     ping类型数据
 *      WebSocket 连接状态：
 *          WEBSOCKET_STATUS_CONNECTION = 1 连接进入等待握手
 *          WEBSOCKET_STATUS_HANDSHAKE = 2  正在握手
 *          WEBSOCKET_STATUS_FRAME = 3      已握手成功过等待客户端发送数据帧
 *
 * 配置选项：
 *      通过 Server::set 传入配置选项：
 *          websocket_subprotocol   设置 WebSocket 子协议。
 *                                  设置后握手响应的 Http 头会增加 Sec-WebSocket-Protocol: {$websocket_subprotocol}
 *                                  具体使用参考 WebSocket 协议相关 RFC 文档
 *
 *          open_websocket_close_frame  启用 WebSocket 协议中关闭帧(opcode 为 0x08)，在 onMessage 回调中接收，默认 false。
 *                                       开启后，可在 onMessage 回调中接收到 client 或 server 发送的关闭帧，可自行对其处理。
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
