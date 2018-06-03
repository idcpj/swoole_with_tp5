<?php

    /**
     * 面向对象的 swool http server 的方法
     * Class http
     */
    class WebSocket{
        const HOST = '0.0.0.0';
        const PORT = 8812;
        const CHART_PORT = 8813;

        public $ws = null;

        public function __construct(){
            $this->ws = new swoole_websocket_server(self::HOST, self::PORT);

            //添加监听端口,用于 聊天室
            $this->ws->listen(self::HOST, self::CHART_PORT, SWOOLE_SOCK_TCP);
            $this->ws->set([
                'work_num'              => 4,
                'task_worker_num'       => 4, //要使用 task  必须设置 task_worker_num 的值
                'document_root'         => '/Users/idcpj/Web/swoole/thinkphp/public/static', //静态文件存放路径
                'enable_static_handler' => true,
            ]);

            $this->ws->on('start', [$this, 'onStart']);
            $this->ws->on('open', [$this, 'onOpen']);
            $this->ws->on('message', [$this, 'onMsg']);
            $this->ws->on('WorkerStart', [$this, 'onWorkStart']);
            $this->ws->on('request', [$this, 'onRequest']);
            $this->ws->on('task', [$this, 'onTask']);
            $this->ws->on('finish', [$this, 'onFinish']);
            $this->ws->on('close', [$this, 'onClose']);
            $this->ws->start();

        }

        public function onStart($server){
            //赋值一个进程名
            //swoole_set_process_name('live_master');  //在 mac 和低版本 linux 上不支持

        }

        /**
         * 监听连接事件
         * @param $ws
         * @param $request
         */
        public function onOpen($ws, $request){
            // 把 fd 放到 redis 中的有序集合中 [1,2,3]
            $id=$request->fd;// 连接 id
            \app\common\lib\Predis::getInstance()->sAdd(config('redis.live_redis_key'), $id);

        }

        /**
         * 推送ws消息事件
         * @param $ws
         * @param $frame
         */
        public function onMsg($ws,$frame){
            echo "ser-push-message".$frame->data."\n";
            $data=[
                'name'=>'cpj',
                'fd'=>$frame->fd
            ];
            $ws->task($data);

            swoole_timer_after(5000, function()use($ws,$frame){
                echo "5s-after\n";
                $ws->push($frame->fd,"server-time-after:");
            });

            $ws->push($frame->fd,'server-push'.date('Y-m-d H:i:s',time()));
        }
        /**
         * http 触发请事件
         * @param $request   请求参数
         * @param $response  返回参数
         */
        public function onRequest($request, $response){



            //把值赋值给$_SERVER,$_GET,$_POST 方便tp5快加执行
            $_SERVER = [];
            if (isset($request->server)) {
                foreach ($request->server as $k => $v) {
                    $_SERVER[strtoupper($k)] = $v;
                }
            }
            //为了防止输出日志的时候出现相同请求阻止favicon.ico 的请求
            if ($_SERVER['REQUEST_URI']=='/favicon.ico'){
                $response->status(404);
                $response->end();
                return;
            }



            if (isset($request->header)) {
                foreach ($request->header as $k => $v) {
                    $_SERVER[strtoupper($k)] = $v;
                }
            }
            $_GET = [];
            if (isset($request->get)) {
                foreach ($request->get as $k => $v) {
                    $_GET[$k] = $v;
                }
            }
            $_POST = [];
            if (isset($request->post)) {
                foreach ($request->post as $k => $v) {
                    $_POST[$k] = $v;
                }
            }
            $_FILES = [];
            if (isset($request->files)) {
                foreach ($request->files as $k => $v) {
                    $_FILES[$k] = $v;
                }
            }
            //写入日志
            $this->writeLog();
            //把 http 对象传过去
            $_POST['http_server']=$this->ws;
            ob_start();
            try{
                think\App::run()->send();
            } catch (Exception $e){
            }
            $res = ob_get_contents();
            ob_end_clean();
            //发送给浏览器
            $response->end($res);
            //$http->close();
        }

        /**
         * 开启workStart
         */
        public function onWorkStart(){
            define('APP_PATH', __DIR__ . '/../../../application/');
            //// 加载基础文件
            require __DIR__ . '/../../../thinkphp/start.php';
        }

        /**
         * 起来才是全局唯一的，不同的worker进程投递的任务ID可能会有相同
         * $task_id和$src_worker_id组合
         * @param $ws
         * @param $task_id
         * @param $src_worker_id
         * @param $data     task 传入的数据
         * @return string
         */
        public function onTask($ws, $task_id, $src_worker_id, $data){


            $task = new \app\common\lib\Task();
            $method = $data['method'];
            $flag = $task->$method($data['data'],$ws);
            return $flag; //告诉 worker
        }

        //接收到的 $data 就是 onTask return 内容
        public function onFinish($ws, $task_id, $data){
            echo "taskId : " . $task_id;
            echo "finish-data-success:{$data}";
        }

        public function onClose($ws, $fd){
            //从redis 中删除 fd
            \app\common\lib\Predis::getInstance()->sRem(config('redis.live_redis_key'), $fd);
        }

        /*
         * 记录日志
         */
        public function writeLog(){
            $datas = array_merge(['date'=>date('Y-m-d H:i:s')],$_GET,$_POST,$_SERVER);

            $log ='';
            foreach ($datas as $key => $value){
                $log .=$key.":".$value." ";
            }
            swoole_async_writefile(APP_PATH.'../runtime/log/'.date('Ym').'/'.date('d').'_access.log', $log,
                function($filename){
                //todo
                },FILE_APPEND);

        }
    }

    $obj = new WebSocket();