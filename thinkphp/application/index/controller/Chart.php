<?php

    namespace app\index\controller;

    use app\common\lib\Util;
    use think\Controller;

    class Chart extends Controller{
        public function index(){
            if (empty($_POST['game_id'])){
                return Util::show("error game_id", [],config('code.error'));
            }
            if (empty($_POST['content'])){
                return Util::show("error content", [],config('code.error'));
            }
            $data=[
                'user'=>"用户".rand(0,2000),
                'content'=>$_POST['content'],
            ];

            //ports[1]  因为连接了连个端口 起始位0 位8812  1位8813
            foreach ($_POST['http_server']->ports[1]->connections as $fd){
                $_POST['http_server']->push($fd,json_encode($data));
            }
            return Util::show("success",[],config('code.success'));


        }
    }
