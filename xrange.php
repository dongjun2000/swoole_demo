<?php

/**
 * 使用生成器来重新实现 range() 函数。
 * 标准的 range() 函数需要在内存中生成一个数组包含每一个在它范围内的值，然后返回该数组，结果就是会产生多个很大的数组。
 * 比如：调用 range(0, 1000000) 将导致内存占用超过 100MB。
 */


/**
 * @param $start
 * @param $limit
 * @param int $step 步长
 * @return Generator
 */
function xrange($start, $limit, $step = 1)
{
    if ($start < $limit) {
        if ($step <= 0) {
            throw new LogicException('Step must be +ve');
        }

        for ($i = $start; $i <= $limit; $i += $step) {
            yield $i;
        }
    } else {
        if ($step >= 0) {
            throw new LogicException('Step must be -ve');
        }

        for ($i = $start; $i >= $limit; $i += $step) {
            yield $i;
        }
    }
}

foreach (xrange(1, 10, 2) as $number) {
    echo "{$number} ";
}

echo PHP_EOL;

foreach (xrange(1, -10, -2) as $number) {
    echo "{$number} ";
}

echo PHP_EOL;

$range = xrange(0, 100, 10);
do {
    var_dump($range->current());
    $range->next();
} while ($range->valid());
