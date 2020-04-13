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
* write_read.php    --- 管道数据读写
* push_pop.php      --- 消息队列通信
* daemon.php        --- 守护进程化
* signal.php        --- 信号监听
* pool.php          --- 进程池

### Swoole 客户端

* client.php        --- 同步阻塞与异步非阻塞

#### 长连接与并行

建立 TCP 长连接

```
$client = new Swoole\Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);

启用 SWOOLE_KEEP 选项后，一个请求结束不会关闭 socket，下一次再进行 connect 时会自动复用上次创建的连接。
如果连接已经关闭，那么 connect 会创建新的连接。

TCP 长连接可以减少 connect、close 带来的额外 IO 消耗，降低服务端 connect、close 次数。
```

Client 并行处理

```
Swoole\Client 的并行处理中用了 select 来做 IO 事件循环。

int swoole_client_select(array &$read, array &$write, array &$error, float $timeout);

$read, $write, $error 分别是可读、可写、错误的文件描述符；是数组变量的引用，元素必须是 Swoole\Client 对象。
此方法基于 select 系统调用，最大支持 1024 个 socket。

$timeout 是 select 系统调用的超时时间，单位秒。
```

### Swoole 高级部分

#### 架构与实现

Swoole 架构

```
Master 进程（运行多线程 Reactor）
    Manager 进程（fork并管理 Worker， Task进程）
        Worker 进程（监听回调，执行业务逻辑，同步阻塞/异步非阻塞）
        Task 进程（接受 Worker 投递的任务，处理完返回，完全同步阻塞）
```

Reactor 线程

```
维护 TCP 连接，处理网络 IO，协议解析，发送给 Worker 数据和接收 Worker 数据，与 Worker 之间使用 UnixSocket 通信。

TCP 与 UDP 差异：
    TCP 客户端，Worker 进程处理完请求，发送给 Reactor 线程，Reactor 发送给客户端。
    UDP 客户端，Worker 进程处理完请求，直接发送给客户端。
```

Reactor、Worker、TaskWorker 之间关系

```
Reactor 可以理解成 Nginx，Worker 就是 php-fpm，TaskWorker 就是数据的异步消费进程。
```

Swoole 实现

```
编写语言                C / C++
Socket实现             Socket 系统调用
IO事件循环              Linux epoll / Mac kqueue
多进程                  fork 系统调用
多线程                  pthread 线程库
线程/进程间消息通知机制    eventfd
信号屏蔽和处理           signalfd
```

#### 高可用与自启动

高可用

```
每分钟定时脚本执行进程监控，Master 进程存活则跳过，如果发现 Master 进程退出了，执行重启逻辑：
先 kill 所有残留子进程，然后重新启动 Server。

有哪些监控形式？
    检测进程名是否存在；
    检测端口是否在监听；
    发送请求探测服务器是否有响应；
    用 supervisor 工具监控进程；
    docker 容器中运行设置参数 --restart=always
    
更多文档@doc:
    https://wiki.swoole.com/wiki/page/233.html
```

自启动

```
通过 systemd 管理服务，编写 service 配置。
需要运行 systemctl --system daemon-reload 重载守护进程生效。
之后可以使用 systemctl 命令管理服务。

更多文档@doc:
    https://wiki.swoole.com/wiki/page/699.html
```

#### MySQL 长连接与连接池

MySQL 短连接

```
请求时连接 MySQL，使用完就释放，不占用 MySQL 服务器连接资源。

程序存在每次请求时连接 MySQL 服务器的开销。 php-fpm 模式的应用程序一般是使用短连接。
```

MySQL 长连接

```
请求完成不释放 MySQL 服务器连接资源。

减少了与 MySQL 服务器建立连接与断开的次数，节省了时间和 IO 消耗，提升了 PHP 程序的性能。
PHP 与 MySQL 建立长连接是使用 pconnect。

PHP MySQL 长连接缺点@doc：
    http://rango.swoole.com/archives/265
```

断线重连

```
长连接需要配合断线重连。
PHP 程序长时间运行，客户端与 MySQL 服务器之间的 TCP 连接是不稳定的。
客户端与 MySQL 服务器的连接会在一些情况下被切断，如：MySQL自动切断连接，回收空闲连接资源，MySQL 重启等。

当 query 返回连接失败（2006/2013）错误码时，执行一次 connect，这样后续的连接已建立，操作就能够执行成功。
```

MySQL 连接池

```
连接池可以有效降低 MySQL 服务器负载。
原理是：共享连接资源，当程序执行完数据库操作，连接会释放给其他的请求使用。

连接池仅在大型应用中才有价值，普通的应用采用 MySQL 长连接方案即可满足需要：
    假设一台机器开启 100 个 php-fpm 进程，并发 100，总共 10 台机器，那么需要 1000 个 MySQL 连接就可以满足需要，压力并不大。
    如果服务器数量达到上百，这时候使用连接池就可以大大降低数据库连接数。
```
