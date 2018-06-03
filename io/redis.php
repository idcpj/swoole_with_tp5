<?php

    $redisClient  =new swoole_redis();
    $redisClient->connect('127.0.0.1', 6379, function (swoole_redis $redisClient,$result){
        echo "connect======".PHP_EOL;
        var_dump($result); //true

        //设置值
        $redisClient->set('now_time',time(),function(swoole_redis $redisClient,$result){
            echo "set value ========".PHP_EOL;
            var_dump($result);//ok
            $redisClient->close();

        });

        //取值
        $redisClient->get('now_time',function(swoole_redis $redisClient,$result){
            echo "get value========".PHP_EOL;
            var_dump($result); //1526094676   now_time 的时间戳
            $redisClient->close();
        });


        //取所有值
        $redisClient->keys("*",function(swoole_redis $redisClient,$result){
            /*array (
              0 => 'now_time',
              1 => 'name',
              2 => 's',
            )  */
            var_export($result);
            $redisClient->close();
        });


    });

    echo "start".PHP_EOL;


