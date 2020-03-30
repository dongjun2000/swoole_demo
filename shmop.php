<?php

/**
 * 进程控制-共享内存
 */

$key = ftok(__FILE__, 't');

echo $key . PHP_EOL;

// 使用 ftok()函数返回的系统ID打开了一个共享内存块
$resource = shmop_open($key, 'c', 0664, 200);

echo "Size of shmop = " . shmop_size($resource) . PHP_EOL;

$bytes = shmop_write($resource, 'Hello', 0);

echo "Bytes of write = " . $bytes . PHP_EOL;

echo shmop_read($resource, 0, 200) . PHP_EOL;

