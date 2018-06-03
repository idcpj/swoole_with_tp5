<?php
    $serv = new swoole_server("127.0.0.1", 9501);
    $serv->set([
        'worker_num'  => 8,  //work 进程数为 cpu 要开启核数的  1-4 倍
        'max_request' => 10000,
    ]);
    //监听连接进入事件
    $serv->on('connect', function ($serv, $fd, $reactor_id){ //$fd 客户端连接的唯一标示    $reactor 线程 id
        echo "Client: reactor_id : {$reactor_id} - fd : {$fd} Connect.\n";
    });
    //监听数据接收事件
    $serv->on('receive', function ($serv, $fd, $reactor_id, $data){
        print_r($data);
        $serv->send($fd, "Server: " . json_encode($data) . " reactor_id : {$reactor_id} - fd : {$fd}");
    });
    //监听连接关闭事件
    $serv->on('close', function ($serv, $fd){
        echo "Client: Close.\n";
    });
    //启动服务器
    $serv->start();