<?php

    /**
     * 面向对象的 swool http server 的方法
     * Class http
     */
    class http{
        const HOST = '0.0.0.0';
        const PORT = 8812;

        public $http = null;

        public function __construct(){
            $this->http = new swoole_http_server(self::HOST, self::PORT);
            $this->http->set([
                'work_num'              => 4,
                'task_worker_num'       => 4, //要使用 task  必须设置 task_worker_num 的值
                'document_root'         => '/Users/idcpj/Web/swoole/thinkphp/public/static', //静态文件存放路径
                'enable_static_handler' => true,
            ]);
            $this->http->on('WorkerStart', [$this, 'onWorkStart']);
            $this->http->on('request', [$this, 'onRequest']);
            $this->http->on('task', [$this, 'onTask']);
            $this->http->on('finish', [$this, 'onFinish']);
            $this->http->on('close', [$this, 'onClose']);
            $this->http->start();

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
            $_FILES=[];
            //把 http 对象传过去
            $_POST['http_server']=$this->http;
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
            define('APP_PATH', __DIR__ . '/../application/');
            //// 加载基础文件
            require __DIR__ . '/../thinkphp/start.php';
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
            $flag = $task->$method($data['data']);
            return $flag; //告诉 worker
        }

        //接收到的 $data 就是 onTask return 内容
        public function onFinish($ws, $task_id, $data){
            echo "taskId : " . $task_id;
            echo "finish-data-success:{$data}";
        }

        public function onClose($ws, $fd){
            echo "clientid:{$fd}\n";
        }
    }

    $obj = new http();