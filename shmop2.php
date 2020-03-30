<?php

$resource = shmop_open('1946229499', 'c', 0664, 200);

echo shmop_read($resource, 0, 200) . PHP_EOL;

// 删除共享内存块
//shmop_delete($resource);
// 关闭共享内存块
shmop_close($resource);

