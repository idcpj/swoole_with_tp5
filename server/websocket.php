<?php
    $server = new swoole_websocket_server("0.0.0.0", 8812);

    $server->set([
        'document_root' => '/Users/idcpj/Web/swoole/data/', //静态文件存放路径
        'enable_static_handler' => true,
    ]);

    $server->on('open', function (swoole_websocket_server $server, $request) {

        echo "server: handshake success with fd: {$request->fd}\n";
    });

    $server->on('message', function (swoole_websocket_server $server, $frame) {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        //push 把数据发送给终端
        $server->push($frame->fd, "this is server");
    });

    $server->on('close', function ($ser, $fd) {
        echo "client {$fd} closed\n";
    });

    $server->start();