<?php

    namespace app\index\controller;

    use app\common\lib\Util;
    use swoole_redis;
    use think\Controller;

    class Index extends Controller{
        public function index(){
           return '';
        }

        public function demo(){
            echo "hello word";
        }

        public function closure($v){
            //return trim($v);
            return $v;
        }

        public function login(){
            $phone = $_GET['phone_num'];
            //生成短信验证码
            $code = rand(100, 999);

            $taskData =[
                'method'=>'sendSms',
                'data'=>[
                    'phone'=>$phone,
                    'code'=>$code,
                ]
            ];
            //发送给 server/http.php task 任务
            $_POST['http_server']->task($taskData);
            return Util::show('发送成功', $taskData['data'],config('code.error'));


        }
    }
