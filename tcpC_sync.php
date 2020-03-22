<?php

/**
 * 同步阻塞tcp客户端
 *
 * connect() / send() / recv() 等方法会等待IO完成后再返回。
 * 同步阻塞操作并不消耗CPU资源，IO操作未完成当前进程会自动转入 sleep 模式，
 * 当IO完成后操作系统会唤醒当前进程*，继续向下执行代码。
 */

use Swoole\Client;

$cli = new Client(SWOOLE_SOCK_TCP);

// 连接服务器
// TCP需要进行 3次握手，所以 connect() 至少需要 3次网络传输过程。
if (!$cli->connect('127.0.0.1', 9501, 0.5)) {
    die('connect failed.');
}

// 向服务器发送数据
// 在发送少量数据时， send() 都可以立即返回的。
// 发送大量数据时，socket缓冲区可能会塞满，send操作会阻塞。
if (!$cli->send("hello\n")) {
    die('send failed.');
}

// 从服务器接收数据
// recv() 操作会阻塞等待服务器返回数据，recv耗时等于服务器处理时间 + 网络传输耗时之和。
if (!$data = $cli->recv()) {
    die("recv failed.");
}

echo $data;

// 关闭连接
$cli->close();
