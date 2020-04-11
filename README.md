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

### coroutine（Swoole协程）

* co.php                    --- CSP编程方式
* coroutine_client.php      --- 协程客户端
* co_runtime.php            --- 网络客户端一键协程
* co_order.php              --- 协程执行流程
* coroutine_exec.php        --- 并发sheel_exec
* co_setDefer.php           --- setDefer机制
* coroutine.php             --- go + chan + defer
* co_chan.php               --- 子协程与通道实现并发请求
* go_defer.php              --- go语言风格的defer
* go_waitGroup.php          --- 实现go语言中的 sync.WaitGroup 功能

#### 协程编程须知

自动创建协程的回调方法：

```
onWorkerStart
onConnect
onOpen
onReceive
redis_onReceive
onPacket
onRequest
onMessage
onPipeMessage
onFinish
onClose
tick/after 定时器

当 enable_coroutine 开启后，以上这些回调和功能会自动创建协程，其余情况可以使用 go() 或者 Coroutine::create() 创建。
@doc https://wiki.swoole.com/wiki/page/949.html
```

与 Golang 协程的区别

```
Swoole4 的协程调度是单线程的，没有数据同步问题，协程间依次执行。
Golang 协程调度器是多线程的，同一时间可能会有多个协程同时执行。

Swoole 禁止协程间公用 Socket 资源，底层会报错，Golang 协程允许同时操作。

Swoole4 的 defer 设计为在协程退出时一起执行，在多层函数中嵌套的 defer 任务按照 先进后出 的顺序执行。
Golang 的 defer 与函数绑定，函数退出时执行。
```

协程异常处理

```
在协程编程中可直接使用 try/catch 处理异常，但必须在协程内捕获，不能跨协程捕获异常。

Swoole-4.2.2 版本以上允许脚本(未创建HttpServer)在当前协程中 exit 退出。
```

协程编程范式

```
协程内部禁止使用全局变量。

协程使用 use 关键字引入外部变量时禁止使用引用(&)。

协程之间通讯必须使用 channel (IPC、Redis 等)。

多个协程公用一个连接、使用全局变量/类的静态变量保存上下文会出现错误。
```


### Swoole共享内存

* table.php         --- Swoole共享内存


### Swoole多进程

* process.php       --- 创建子进程