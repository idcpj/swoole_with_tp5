<?php

    namespace app\admin\controller;

    use app\common\lib\Predis;
    use app\common\lib\Util;
    use think\Controller;

    class Live extends Controller{

        public function push(){
            //获取后端接收的直播信息,
            if (empty($_GET)) {
                return Util::show("参数错误", [], config('code.error'));
            }
            $teams = [
                1 => [
                    'name' => '马刺',
                    'logo' => '../live/imgs/team1.png',
                ],
                2 => [
                    'name' => '火箭',
                    'logo' => '../live/imgs/team2.png',
                ],
            ];
            $data['type'] = intval($_GET['type']);
            $data['title'] = !empty($teams[$_GET['team_id']]['name']) ?$teams[$_GET['team_id']]['name']: '直播员';
            $data['logo'] = !empty($teams[$_GET['team_id']]['logo']) ?$teams[$_GET['team_id']]['logo']: '';
            $data['content'] = !empty($_GET['content']) ?$_GET['content']: '';
            $data['image'] = !empty($_GET['image']) ?$_GET['image']: '';



            //使用 task 任务发送
            $taskData =[
                'method'=>'pushLive',
                'data'=>$data,
            ];

            $_POST['http_server']->task($taskData);
            return Util::show('发送成功', [],config('code.success'));


            //<editor-fold desc="普通方法">

            //获取连接的 id
            //$clients = Predis::getInstance()->sMembers(config('redis.live_redis_key'));
            //使用普通推送  推送给 html 页面
            //foreach ($clients as $client) {
            //    $_POST['http_server']->push((int)$client,json_encode($data));
            //
            //}

            //</editor-fold>

            //赛况的基本信息入库
            //todo
        }

    }
