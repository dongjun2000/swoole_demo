<?php

/**
 * 创建一个HTTP服务器
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