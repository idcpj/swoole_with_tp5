<?php
    namespace app\common\lib;

    class Redis {
        /**
         * 验证码的前缀
         * @var string
         */
        static protected $pre = 'sms_';
        /**
         * 用户前缀
         * @var string
         */
        static protected $userPre = 'user_';

        /**
         * 存储验证码 redis key
         * @param $phone
         * @return string
         */
        static public function smsKey($phone){
            return self::$pre.$phone;
        }

        /**
         * 返回用户的 key
         * @param $phone
         * @return string
         */
        static public function userKey($phone){
            return self::$userPre.$phone;
        }

    }

