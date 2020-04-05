<?php

/**
 * 创建一个HTTP服务器
 *
 * Swoole\Http\Server 继承自 Swoole\Server，是一个 HTTP 服务器，父类提供的 API 都可使用，它支持同步和异步两种模式。
 * 两种模式都支持维持大量 TCP 客户端连接，同步 / 异步 仅仅体现在对请求的处理方式上。
 *
 * Swoole\Http\Server 对 Http 协议的支持不完整，一般用作应用服务器，使用 Nginx 作为代理。
 *
 * 同步模式：
 *      等同于 php-fpm 模式，需要设置大量的 worker 进程来完成并发请求处理。编程方式与普通 PHP Web程序一致。
 *      与 php-fpm 不同的是，服务器可以应对大量客户端并发连接，类似于 Nginx。
 *
 * 异步模式：
 *      这种模式下整个服务器是异步非阻塞的，服务器可以应对大量并发连接和并发请求，
 *      但编程方式要使用异步 API，否则会退化为同步模式。
 *      * Swoole-4.3 版本已移除异步模块，建议使用 Coroutine 模式。
 *
 * 配置选项：
 *      除了可以设置 Server 相关选项外，可以设置 HTTP 服务器独有的选项。
 *      upload_tmp_dir              上传文件临时目录，目录长度有限制。
 *      http_parse_post             设置 POST 消息解析开关。
 *      http_parse_cookie           关闭时将在 header 中保留原始 cookie 信息。
 *      http_compression            启用压缩，默认开启。
 *      document_root               配置静态文件根目录。
 *      enable_static_handler       开启静态文件请求处理功能，配合 document_root
 *      static_handler_locations    设置静态文件的路径。
 *
 * 回调函数：
 *      1.与 Swoole\Server->on 相同，使用 on 方法注册事件回调。
 *      2.Swoole\Http\Server->on 不接受 onConnect / onReceive 回调设置。
 *      3.Swoole\Http\Server->on 接受独有的 onRequest 事件回调。
 *
 * HTTP Server 参数接收响应：
 *      Swoole\Http\Request：
 *          1.Http 请求对象，保存了客户端请求相关信息。
 *          2.属性：
 *              $header, $server, $get, $post, $cookie, $files
 *          3.方法：
 *              rawContent() 获取原始的 POST 包体
 *              getData() 获取完整的原始 Http 请求报文
 *          4.属性描述@doc：
 *              https://wiki.swoole.com/wiki/page/328.html
 *
 *      Swoole\Http\Response：
 *          1.Http 响应对象，通过调用响应对象的方法来实现 Http 响应发送。
 *          2.当 Response 对象销毁时，如果未调用 end 发送响应，底层会自动执行 end。
 *          3.方法描述@doc：
 *              https://wiki.swoole.com/wiki/page/329.html
 *
 * HTTP Server 常见问题：
 *      1.Chrome 产生两次请求
 *          Chrome 浏览器会自动请求一次 favicon.ico，
 *          可通过 $request->server 属性的 request_uri 键获取 URL 路径进行判断处理。
 *
 *      2.GET、POST 请求尺寸
 *          GET 请求头有尺寸限制，不可更改，如果请求不是正确的 Http 请求，将会报错。
 *          POST 请求尺寸受到 package_max_length 限制。
 *
 *
 * Http服务器只需要关注请求响应即可，所以只需要监听一个 onRequest 事件。
 * 当有新的Http请求进入就会触发此事件。
 *
 * 事件回调函数有2个参数：
 *      request：包含了请求的相关信息，如GET/POST请求的数据。
 *      response： 对request的响应可以通过操作response对象来完成。
 *          end(html)：输出html内容，并结束此请求。
 *          header(key, value)：输出自定义的header头信息。
 */

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

// 创建一个 http server 对象，声明监听的ip:port
$http = new Server('0.0.0.0', 9501);

// 监听请求对象 request
$http->on('request', function (Request $request, Response $response) {
    // Chrome请求两次问题： 使用Chrome访问服务器，会产生额外的一次请求，/favicon.ico
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        // status()：设置状态码为404
        $response->status(404);
        // 直接结束本次请求
        return $response->end();
    }

//    var_dump($request->get, $request->post, $request->server);
//    $response->header('Content-Type', 'text/html; charset=utf-8');
//    // end()：表示输出一段HTML内容，并结束此请求。
//    $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');

    // URL路由 http://localhost:9501/test/index
    list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
    var_dump($controller, $action);
    // 可以注册自动加载类
    // PHP类名和函数名不区分大小写
    (new $controller)->$action($request, $response);
});

// 启动HTTP服务器
$http->start();

class Test
{
    public function index($request, $response)
    {
        var_dump($request->get, $request->post);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>test/index #' . rand(1000, 9999) . '</h1>');
    }

    public function say($request, $response)
    {
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>test/say #' . rand(1000, 9999) . '</h1>');
    }
}