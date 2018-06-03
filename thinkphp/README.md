
## script/bin/server 目录
在 server 目录中搭建 swoole 的 server 服务
`http_server.php` 为面向过程的 swoole http 服务 
`http.php`为面向过程的 swoole http 服务 
`websocket.php`  websocket 服务   -  此为真正的服务

## script/bin/moniter
监听 websockt 端口是否正常开启
`php thinkphp/script/moniter/Server.php`  即可开启监听
`nohup /usr/local/bin/php /Users/idcpj/Web/swoole/thinkphp/script/moniter/Server.php  > /Users/idcpj/Web/swoole/thinkphp/script/moniter/log.txt & `    最后加`&` 表示在后台开启
查看后台是否运行 `ps aux | grep moniter/setver.php`

## thinkphp/application/common/lib  放置模块化的方法

Predis.php  redis 的 set 和 get 操作

Redis.php    redis 的set 时的前缀封装  如`user_`  ,`sms_`

Sms.php     sms  发送短信

Task.php    封装 task 方法  如 `异步发送短信`

Util.php     如 show 等回调方法

##  测试运行
### 运行 图文直播
`php thinkphp/script/bin/server/WebSocket.php`
在`http://localhost:8812/admin/live.html`中输入内容
打开多个`http://localhost:8812/live/detail.html`接受数据

### 运行聊天室
`php thinkphp/script/bin/server/WebSocket.php`
在多个`http://localhost:8812/live/detail.html`输入内容.测试输出


## 平滑重启
由于此时 更改 php 代码都需要重新运行,故可以使用平滑重启
在`thinkphp/script/bin/server/WebSocket.php`中 start 事件中 给改进程命名,
然后在`thinkphp/script/bin/server/reload.sh` 中编写代码
当更改完代码后 执行`sh reload.sh` 即可