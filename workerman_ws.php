<?php

/**
 * Workerman 实时统计系统信息
 *
 * 客户端：workerman_tj.html
 */

require __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

$ws = new Worker('websocket://0.0.0.0:9502');

$ws->count = 1;
$ws->reusePort = true;

$ws->onConnect = function ($connection) {
    Timer::add(1, function () use ($connection) {
        exec('uptime', $load);
        exec('free -h', $memory);
        exec('df -h', $disk);

        $data = json_encode([
            'load'   => $load,
            'memory' => $memory,
            'disk'   => $disk,
        ]);

        $connection->send($data);
    });
};

Worker::runAll();
