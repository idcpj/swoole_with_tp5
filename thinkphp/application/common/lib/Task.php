<?php

    namespace app\common\lib;

    /**
     * 所有的 swoole 的 task 操作都放到这个 task 类中
     * Class Task
     * @package app\common\lib
     */
    class Task{

        /*
         * 异步发送验证码
         */
        public function sendSms($data,$ws){
            try{
                $respone = Sms::sendPhone($data['phone']);

                //如果发送失败
                if ( ! $respone) {
                    return Util::show("error", '', config('code,error'));
                }

                Predis::getInstance()->set(Redis::smsKey($data['phone']), $data['code'], (int)config('redis.time_out'));

            } catch (\Exception $e){
                echo $e->gedatatMessage();
            }
        }

        //推送到网页的 websocket
        public function pushLive($data,$ws){
            //获取连接 id
            $clients = Predis::getInstance()->sMembers(config('redis.live_redis_key'));
            try{
                foreach ($clients as $client) {
                    $ws->push((int)$client, json_encode($data));
                }
            } catch (\Exception $e){
                print_r($e->getMessage());
                return Util::show($e->getMessage(),[],config('code.error') );
            }
        }
    }

