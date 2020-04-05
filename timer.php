<?php

/**
 * 设置定时器
 *
 * Swoole提供了类似 JavaScript 的 setInterval/setTimeout 异步高精度定时器，粒度为毫秒级。
 *
 * 底层基于 epoll_wait(异步进程) 和 settimer（同步进程）实现，数据结构使用最小堆，可支持添加大量定时器。
 *
 * 底层不支持时间参数为 0 的定时器。
 *
 * Swoole\Timer::tick()函数就相当于setInterval，是持续触发的。
 * Swoole\Timer::after()函数就相当于setTimeout，仅在约定的时间触发一次。
 * Swoole\Timer::tick() 和 Swoole\Timer::after()函数会返回一个整数，表示定时器的ID。
 * 可以使用 Swoole\Timer::clear(int $timer_id) 清除此定时器，参数为定时器ID。
 */

$i = 0;

// 间隔时钟定时器
// 每隔2000ms触发一次
// 类似 JavaScript的 setInterval
Swoole\Timer::tick(2000, function ($timer_id, $param1, $param2) use (&$i) {
    $i++;

    echo $i . PHP_EOL;

    echo $param1 . ' - ' . $param2 . PHP_EOL;

    if ($i == 5) {
        Swoole\Timer::clear($timer_id);
    }
}, 'A', 'B');

// 一次性定时器
// 3000ms后执行此函数
// 类似 JavaScript的 setTimeout
Swoole\Timer::after(3000, function () {
    echo "after 3000ms.\n";
});
