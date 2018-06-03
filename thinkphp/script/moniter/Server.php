<?php

    /**
     * 监控服务  ws http 8811
     * Class Server
     */
    class Server{
        const PORT = 8812;

        public function port(){
            /**
             *  grep LISTEN  只显示端口
             *  wc -l  显示行数 有几行  通过有如果存在一行,说明端口开启
             */
            $shell = "netstat -an | grep ".self::PORT.'| grep LISTEN | wc -l';  //
            $result = shell_exec($shell);
            /*$result 输出
             *  tcp4       0      0  127.0.0.1.8812         127.0.0.1.59006        ESTABLISHED
                tcp4       0      0  127.0.0.1.59006        127.0.0.1.8812         ESTABLISHED
                tcp4       0      0  *.8812                 *.*                    LISTEN
             */
            if ($result!=1){
                //处理监听未开启的操作  如:发邮箱,发短信
                //todo
                echo date('Y-m-d H:i:s',time())."   error   ".PHP_EOL;
            }else{
                echo date('Y-m-d H:i:s',time())."   success ".PHP_EOL;

            }
        }

    }


    ///每两秒检测 是否开启
    swoole_timer_tick(2000, function($timeid){
        (new Server())->port();
    });
