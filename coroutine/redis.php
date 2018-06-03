<?php

    $http = new swoole_http_server('0.0.0.0', 8001);
    $http->set([
        'document_root' => '/Users/idcpj/Web/swoole/data/', //静态文件存放路径
        'enable_static_handler' => true,
    ]);

    $http->on('request', function ($request,$respone){
        $redis = new Swoole\Coroutine\Redis();
        $redis->connect('127.0.0.1', 6379);
        $value = $redis->get($request->get['key']);
        $respone->header('Content-Type','text/plain');
        $respone->end($value);
    });
    $http->start();

