<?php

/**
 * 爬虫 - 图片采集
 *
 * 安装依赖包：
 *      composer require fabpot/goutte --prefer-dist
 */

require __DIR__ . '/vendor/autoload.php';

use Goutte\Client;

$client = new Client();

$links = [
    'http://www.nipic.com/topic/show_27192_1.html',
    'http://www.nipic.com/topic/show_27054_1.html',
    'http://www.nipic.com/topic/show_27085_1.html',
];

$pids = [];

foreach ($links as $url) {
    $pid = pcntl_fork();
    switch ($pid) {
        case -1:
            die("Fork failed\n");
            break;
        case 0:
            // 子进程
            $id = posix_getpid();
            $data = [];

            $crawler = $client->request('GET', $url);
            $crawler->filter('.search-works-thumb')->each(function ($node) use ($client, $id, &$data) {
                $url = $node->link()->getUri();

                $crawler = $client->request('GET', $url);
                $crawler->filter('#J_worksImg')->each(function ($node) use ($id, &$data) {
                    $src = $node->image()->getUri();

                    $data[$id][] = $src;
                });
            });
            print_r($data);
            exit;      // 子进程退出
            break;
        default:
            // 父进程
            // 给$pids赋值必须在主进程当中，因为进程的内存空间本身是独立的。
            $pids[$pid] = $pid;
            break;
    }
}

while (count($pids)) {
    // 回收子进程
    if (($id = pcntl_wait($status, WUNTRACED)) > 0) {       // 阻塞，等待子进程结束，防止子进程成为僵尸进程
        unset($pids[$id]);
    }
}

echo "Done\n";


