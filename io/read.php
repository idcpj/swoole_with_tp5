<?php

    swoole_async_readfile(__DIR__.'/read.txt', function($filename,$fileContent){
        echo 'fileName:',$filename.PHP_EOL;
        echo "content:".$fileContent.PHP_EOL;
    });

    echo "over!!".PHP_EOL;


