<?php
    /**
     *面向过程的 http 服务器方法
     * http.php 为面向对象的方法,推荐使用 htto.php 中的方法
     */
    
    
    
    $http = new swoole_http_server('0.0.0.0', 8812);
    $http->set([
        'document_root'         => '/Users/idcpj/Web/swoole/thinkphp/public/static', //静态文件存放路径
        'enable_static_handler' => true,
    ]);
    $http->on('WorkerStart', function (swoole_server $server){
        define('APP_PATH', __DIR__ . '/../application/');
        //// 加载基础文件
        require __DIR__ . '/../thinkphp/base.php';
    });
    //在 request 中执行代码
    $http->on('request', function ($request, $response) use ($http){
        //把值赋值给$_SERVER,$_GET,$_POST 方便tp5快加执行
        $_SERVER=[];
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        $_GET=[];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_POST=[];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        ob_start();
        try{
            think\App::run()->send();
        } catch (Exception $e){
            //todo
        }
        $res = ob_get_contents();
        ob_end_clean();
        //发送给浏览器
        $response->end($res);
        //$http->close();

    });
    $http->start();