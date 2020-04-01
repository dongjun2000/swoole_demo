<?php

/**
 * Swoole聊天程序
 */

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

$ws = new Server('0.0.0.0', 9502);

$ws->set([
    'worker_num' => 2,
]);

// 收到数据时触发
$ws->on('message', function (Server $ws, Frame $frame) {
    // 当前客户端标识
    $current_fd = $frame->fd;
    // 客户端发来的数据
    $data = $frame->data;
    // 给所有客户端发送数据
    foreach($ws->connections as $fd) {
        $ws->push($fd, "Client-{$current_fd}: " . $data);
    }
});

$ws->start();
