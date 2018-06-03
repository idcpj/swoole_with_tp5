echo loading
pid= `pidof live_master` #查看在 websocket.php 中  start 事件设置的进程名称
echo $pid
kill -USR1 $pid     # 平滑重启
echo "loading success"