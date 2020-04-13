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

### Swoole其它

#### 守护进程常用数据结构

SqlQueue 队列

```
PHP 的 SPL 标准库中提供了 SplQueue 内置的队列数据结构。
使用队列 (Queue) 实现生产者消费者模型，解决并发排队问题。
大并发服务器程序建议使用 SplQueue 作为队列数据结构，性能比 Array 模拟的队列高。
```

SplHeap 堆

```
在服务器程序开发中经常要用到排序功能，如会员积分榜。
普通的 array 数据结构，使用 sort 进行排序，即使使用了最快的快速排序方法，实际上也会存在较大的计算开销。
因此在内存中维护一个有序的内存结构可以有效地避免 sort 排序的计算开销。

在 PHP 中 SplHeap 就是一种有序的数据结构。数据总是按照最小在前或最大在前排序。新插入的数据会自动进行排序。
```

SplFixedArray 定长数组

```
PHP 的 SPL 标准库中提供了一个定长数组结构。
和普通 PHP 数组不同，定长数组读写性能更好，但只支持数字索引的访问方式,可以使用 setSize 方式动态改变定长数组尺寸。
```

#### 日志等级控制

日志等级设置

```
通过使用 Server 的 set 方法设置 log_level 和 trace_flags 选项来控制日志等级。

$server->set([
    'log_level' => 'SWOOLE_LOG_ERROR',
    'trace_flags' => 'SWOOLE_TRACE_SERVER | SWOOLE_TRACE_HTTP2',
]);
```

日志级别 log_level

```
SWOOLE_LOG_DEBUG
SWOOLE_LOG_TRACE
SWOOLE_LOG_INFO
SWOOLE_LOG_NOTICE
SWOOLE_LOG_WARNING
SWOOLE_LOG_ERROR
```

跟踪标签 trace_flags

```
设置跟踪日志的标签，多个使用 | 操作符，可使用 SWOOLE_TRACE_ALL 跟踪所有项目。

SWOOLE_TRACE_SERVER, SWOOLE_TRACE_CLIENT, SWOOLE_TRACE_BUFFER, SWOOLE_TRACE_CONN, ……

更多文档@doc：
    https://wiki.swoole.com/wiki/page/936.html 
```

#### Swoole辅助函数

设置进程名称

```
void swoole_set_process_name(string $name)
可用于 PHP5.2 以上版本，此函数与 PHP5.5 提供的 cli_set_process_title 功能是相同的，
兼容性比 cli_set_process_title 要差，优先使用 cli_set_process_title。
```

SWOOLE 扩展版本

```
string swoole_version()
返回当前执行的 PHP 安装的 Swoole 扩展版本。
```

错误码转换成错误信息

```
string swoole_strerror(int $errno, int $error_type = 1)
```

最近一次系统调用的错误码

```
int swoole_errno()
```

获取最近一次 SWOOLE 底层的错误码

```
int swoole_last_error()
```

获取本机所有网络接口的 IP 地址

```
array swoole_get_local_ip()
```

清除 SWOOLE 内置的 DNS 缓存

```
void swoole_clear_dns_cache()

对 swoole_client 和 swoole_async_dns_lookup 有效。
```

获取本机网卡 Mac 地址

```
void swoole_get_local_mac()
调用成功返回所有网卡的 Mac 地址。
```

获取本机 CPU 核数

```
int swoole_cpu_num()
调用成功返回 CPU 核数。
```

#### PHP选项与内核参数

php.ini 选项

```
swoole.enable_coroutine     使用 On、Off 开关内置协程，默认开启
swoole.display_errors       用于关闭、开启 Swoole 错误信息，默认开启
swoole.use_shortname        是否启用短别名，默认开启
swoole.socket_buffer_size   设置进程间通信 socket 缓存区尺寸，默认为8M
```

ulimit 设置

```
ulimit –n 调整为 100000 或更大，或通过编辑文件  /etc/security/limits.conf，
修改文件需要重启系统生效。
```

三种方式设置内核参数

```
1.修改 /etc/sysctl.conf 加入配置选项
    保存后调用 sysctl -p/-f 加载新配置，操作系统重启后自动生效。
2.使用 sysctl 命令临时修改
    如 sysctl -w net.ipv4.tcp_mem=379008，操作系统重启后失效。
3.修改 /proc/sys/ 目录中的文件
    如 echo 379008 > /proc/sys/net/ipv4/tcp_mem，操作系统重启后失效。
```

内核参数：net.unix.max_dgram_qlen

```
控制数据报套接字接收队列最大长度，Swoole 进程间通信使用 Unix Socket Dgram，
请求量大需要调大此参数，系统默认为 10。
```

网络内核设置：调整缓冲区大小

```
net.core.rmem_default=262144    默认的 socket 接收缓冲区大小
net.core.wmem_default=262144    默认的 socket 发送缓冲区大小
net.core.rmem_max=262144        最大的 socket 接收缓冲区大小
net.core.wmem_max=262144        最大的 socket 发送缓冲区大小

根据网络延迟情况适当调大这些值
```

网络内核设置：使用 TCP keepalive

```
net.ipv4.tcp_keepalive_time
net.ipv4.tcp_keepalive_intvl
net.ipv4.tcp_retries2
net.ipv4.tcp_syn_retries
```

内核参数：net.ipv4.tcp_tw_reuse

```
Server 重启时，允许将 TIME-WAIT 的 socket 重新用于新的 TCP 连接。
默认 0 表示关闭。
```

消息队列设置

```
kernel.ksgmnb = 4203520     消息队列的最大字节数
kernel.msgmni = 64          最多允许创建多少个消息队列
kernel.msgmax = 8192        消息队列单条数据最大的长度

如果 Swoole Server 使用了消息队列作为通信方式，建议适当调大这些值
```