<?php

    echo "process-start-time ". date('Y-m-d H:i:s',time()).PHP_EOL;

    //传统获取 url 中的内容
    $url =[
        'http://baidu.com',
        'http://sina.com.cn',
        'http://qq com',
        'http://baidu.com?search=singa',
        'http://baidu.com?search=singwa2',
        'http://baidu.com?search=imooc',
    ];

    /*
    foreach ($url as $v){
        $content []=curlData($v);
    }
    */

    for ($i=0; $i <6 ; $i++) {
        //子进程
        $process = new swoole_process(function (swoole_process $worker) use($i,$url){
           $content =  curlData($url[$i]);
           //方法1 输出到管道中
           echo $content.PHP_EOL;
           //方法2 输出到管道中
           $worker->write($content);
        },true);
    	$pid = $process->start();

    	$works[$pid] = $process;  //把进程的内容放入 $works 中,这样 可以通过进程的 swoole_process->read  获取内容
    }

    //获取管道中的内容
    foreach ($works as $process){
        echo $process->read();
    }

    function curlData($url){
        sleep(1);//假设耗时一秒
        return $url.'success'.PHP_EOL;
    }

    echo "process-end-time ". date('Y-m-d H:i:s',time()).PHP_EOL;

    //总耗时只有 1秒 而非 6s



