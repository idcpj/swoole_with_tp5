<?php

    //连接 swoole_tcp服务

    $client = new swoole_client(SWOOLE_SOCK_TCP);

    if (!$client->connect("127.0.0.1",9501)) {
         echo "连接失败";
         die();
    }
    //php cli 常量
    fwrite(STDOUT, "请输出消息:");
    $msg = trim(fgetc(STDOUT));

    //把消息发送给 tcp     server

    $client->send($msg);

    //接受来自 server 的数据
    $result = $client->recv();

    echo "输出信息:".$result;


