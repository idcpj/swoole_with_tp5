<?php
    $procoess = new swoole_process(function (swoole_process $pro){

        echo  "not output to term";
        //开启http_server.php 的进程
        $pro->exec('/usr/local/bin/php', [__DIR__ . "/../server/http_server.php"]);

    },true);//如果第二个参数为 true  输出不会输出到屏幕中
    $pid = $procoess->start();

    //子进程
    echo $pid . PHP_EOL;


    //回收结束运行的子进程。 如上面代码的 http_server.php
    swoole_process::wait();