<?php

    namespace app\index\controller;

    use app\common\lib\Predis;
    use app\common\lib\Redis;
    use app\common\lib\Util;
    use think\Controller;

    class Login extends Controller{
        public function index(){
            $phoneNum  = intval($_GET['phone_num']);
            $code = intval($_GET['code']);
            //参数错误
            if (!$code || !$phoneNum){
                return Util::show("phone or code is empty", [],config('code.error'));
            }

            try{
                //比较验证码
                $prides = Predis::getInstance();
                $key = Redis::smsKey($phoneNum);
                $redisCode = $prides->get($key);
                if ((int)$redisCode!==(int)$code){
                    return Util::show("验证码不出正确", '',config('code.error'));
                }
                $data =[
                    'user'=>$phoneNum,
                    'srckey'=>md5(Redis::userKey($phoneNum)),
                    'time'=>time(),
                    'isLogin'=>true,
                ];
                $prides->set(Redis::userKey($phoneNum), $data);
                return Util::show("success", '',config('code.success'));

            } catch (\Exception $e){
                return Util::show($e->getMessage(), '',config('code.error'));

            }

        }
    }
