<?php

/**
 * 设置定时器
 *
 * Swoole提供了类似 JavaScript 的 setInterval/setTimeout 异步高精度定时器，粒度为毫秒级。
 *
 * Swoole\Timer::tick()函数就相当于setInterval，是持续触发的。
 * Swoole\Timer::after()函数就相当于setTimeout，仅在约定的时间触发一次。
 * Swoole\Timer::tick() 和 Swoole\Timer::after()函数会返回一个整数，表示定时器的ID。
 * 可以使用 Swoole\Timer::clear(int $timer_id) 清除此定时器，参数为定时器ID。
 */

// 每隔2000ms触发一次
// 类似 JavaScript的 setInterval
Swoole\Timer::tick(2000, function ($timer_id) {
    echo "tick-2000ms\n";
});

// 3000ms后执行此函数
// 类似 JavaScript的 setTimeout
Swoole\Timer::after(3000, function () {
    echo "after 3000ms.\n";
});
