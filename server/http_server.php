<?php

    $http = new swoole_http_server('0.0.0.0', 8811);

    $http->set([
        'document_root' => '/Users/idcpj/Web/swoole/data/', //静态文件存放路径
        'enable_static_handler' => true,
    ]);

    $http->on('request',function ($request,$response){
        $conent = [
            'date:'=>date('Y-m-d H:i:s',time()),
            'get:'=>$request->get,
            'post:'=>$request->post,
            'header:'=>$request->header,
        ];
        swoole_async_writefile(__DIR__ . '/access.log', json_encode($conent,JSON_UNESCAPED_UNICODE), function (){},
            FILE_APPEND);

        //发送给浏览器
        $response->end('<h1>HTTPserver</h1>');
    });

    $http->start();