<?php

/**
 * 协程执行流程
 * 
 * 遵循原则：
 *      1.协程没有 IO 等待的执行 PHP 代码，不会产生执行流程切换。
 *      2.协程遇到 IO 等待立即将控制权切换，IO 完成后，重新将执行流切回切出点。
 *      3.协程并发，依次执行，其余同上。
 *      4.协程嵌套执行，流程由外向内逐层进入，直到发生IO，然后切到外层协程，
 *          注意，父协程不会等待子协程结束。
 * 
 */

Swoole\Runtime::enableCoroutine(true);

Swoole\Coroutine::set([
    'max_coroutine' => 2000,
]);


go(function () {
    echo "main co start " . co::getcid() . PHP_EOL;

    go(function () {
        echo "child co start " . co::getcid() . PHP_EOL;

        sleep(2);

        echo "child co end " . co::getcid() . PHP_EOL;
    });

    go(function () {
        echo "child co start " . co::getcid() . PHP_EOL;

        sleep(1);

        echo "child co end " . co::getcid() . PHP_EOL;
    });

    echo "main co end " . co::getcid() . PHP_EOL;
});

echo "end" . PHP_EOL;


/**
main co start 1
child co start 2
child co start 3
main co end 1
end
child co end 3
child co end 2
 */
