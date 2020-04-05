### PHP多进程

* process_first.php     --- 第一个PHP多进程程序
* process_exec.php      --- 用子进程执行外部命令
* process_monitor.php   --- 监控子进程
* process_daemon.php    --- 守护进程化
* process_signal.php    --- 任务控制信号
* shmop.php、shmop2.php  --- 进程控制 - 共享内存
* echo_server.php、echo_client.php --- 进程控制 - 网络套接字(一)
* stream_echo_server.php、stream_echo_client.php ---进程控制 - 网络套接字(二)
* crawler.php           --- 实战-图片采集

* echo_multiplex.php    --- IO多路复用

### PHP协程

* yield.php、xrange.php  --- 生成器使用
* schedule.php          --- 多任务调度

### 长连接项目示例

* workerman_tj.html、workerman_ws.php    --- Workerman实时统计
* swoole_chat.html、swoole_ws.php        --- Swoole聊天程序

## SWOOLE

Swoole 是面向生成环境的PHP异步网络通信引擎，使 PHP开发人员可以编写高性能的异步并发TCP、UDP、Unix Socket、HTTP 和 WebSocket服务。

Swoole 可以广泛应用于互联网、移动通信、企业软件、云计算、网络游戏、物联网、车联网和智能家居等领域。

使用 PHP+SWOOLE 作为网络通信框架，可以使企业IT研发团队的效率大大提升，更加专注于开发创新产品。

Swoole 与 php-fpm 在HTTP方面的差异：

```
Nginx -> PHP-FPM -> 加载框架，同步阻塞执行（返回结果）
[Nginx] -> SwooleHttpServer -> 同步阻塞/非阻塞/协程执行（返回结果）
```
* PHP-FPM 是后台多进程模型，但是只用来解析PHP脚本，没有Web服务器支持无法处理HTTP请求。
* SwooleHttpServer 实现了HTTP协议解析，C语言实现，应用常驻内存，性能很高，并且支持了很多其它高级特性。

### 编程须知

* sleep.php     --- 睡眠函数的影响
* exit.php      --- 中止函数的影响
* while.php     --- 死循环的影响
* rand.php      --- 随机函数的影响
* isolation.php --- 进程隔离

### TCP

* tcpS.php       --- 创建TCP服务器
* tcp_socket.php --- PHP的原生socket实现
* tcp_sync.php   --- 同步tcp客户端
* tcp_async.php  --- 异步tcp客户端

### UDP

* udpS.php       --- 创建UDP服务器
* udpC.php       --- 创建UDP客户端

### Server四层生命周期

* lifecycle.php  --- Server四层生命周期

### 事件回调函数详解

* callback.php   --- 事件回调函数详解

### http

* http.php       --- 创建HTTP服务器

### WebSocket

* websocket.php  --- 创建WebSocket服务器
* websocket.html --- 创建WebSocket客户端

### Redis 服务器

* redis.php      --- 创建Redis服务器

### TaskWorker

* taskS.php      --- 执行异步任务

### 定时器

* timer.php      --- 使用定时器

### 网络通信协议设计

* protocol.php   --- 网络通信协议设计

### 多进程共享数据

* multiprocess_share_data.php

### coroutine

#### 协程客户端

* coroutine_client.php

#### 并发sheel_exec

* coroutine_exec.php

#### go + chan + defer

* coroutine.php

#### go语言风格的defer

* go_defer.php

#### 实现 go语言中的 sync.WaitGroup 功能

* go_waitGroup.php