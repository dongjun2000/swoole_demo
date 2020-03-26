<?php

/**
 * 协程：实现 sync.WaitGroup 功能
 *
 * 在 Swoole4 中可以使用channel实现协程间的通信、依赖管理、协程同步。
 * 基于 channel 可以很容易地实现 Golang 的 sync.WaitGroup 功能。
 */

class WaitGroup
{
    private $count = 0;
    private $chan;

    public function __construct()
    {
        $this->chan = new chan();
    }

    // 增加计数
    public function add()
    {
        $this->count++;
    }

    // 任务已完成
    public function done()
    {
        $this->chan->push(true);
    }

    // 等待所有任务完成恢复当前协程的执行
    public function wait()
    {
        while ($this->count--) {
            $this->chan->pop();
        }
    }
}

// 使用上面定义的WaitGroup
go(function () {
    $wg = new WaitGroup();
    $result = [];

    $wg->add();
    // 启动第一个协程
    go(function () use ($wg, &$result) {
        $cli = new \Swoole\Coroutine\Http\Client('www.taobao.com', 443, true);
        $cli->setHeaders([
            'Host'            => 'www.taobao.com',
            'User-Agent'      => 'Chrome/49.0.2587.3',
            'Accept'          => 'text/html; application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => 1]);
        $cli->get('/');
        $result['www.taobao.cn'] = $cli->statusCode;
        $cli->close();

        $wg->done();
    });

    $wg->add();
    // 启动第二个协程
    go(function () use ($wg, &$result) {
        $cli = new \Swoole\Coroutine\Http\Client('www.mi360.cn', 443, true);
        $cli->setHeaders([
            'Host'            => 'www.mi360.cn',
            'User-Agent'      => 'Chrome/49.0.2587.3',
            'Accept'          => 'text/html; application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => 1]);
        $cli->get('/');
        $result['www.mi360.cn'] = $cli->statusCode;
        $cli->close();

        $wg->done();
    });

    // 挂起当前协程，等待所有任务完成后恢复
    $wg->wait();
    // 这里 $result 包含了 2 个任务执行结果
    var_dump($result);
});
