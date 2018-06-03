<?php
    $file_content ="test 要写入的内容".PHP_EOL;

    swoole_async_writefile(__DIR__.'/test.log', $file_content, function($filename) {
        echo "wirte ok.{$filename}".PHP_EOL;
    }, FILE_APPEND);

