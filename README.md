# swoole_demo

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

### TCP

#### 创建TCP服务器

* tcpS.php

#### 创建TCP客户端

* tcp_socket.php --- PHP的原生socket实现
* tcp_sync.php   --- 同步tcp客户端
* tcp_async.php  --- 异步tcp客户端

### UDP

#### 创建UDP服务器

* udpS.php

#### 创建UDP客户端

* udpC.php

### http

#### 创建HTTP服务器

* http.php

### WebSocket

#### 创建WebSocket服务器

* websocket.php

#### 创建WebSocket客户端

* websocket.html  

### TaskWorker

#### 执行异步任务

* taskS.php

### 定时器

* timer.php

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