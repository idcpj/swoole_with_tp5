<?php
    namespace app\common\lib;
    use Exception;

    class Predis {
        static private $_instance=null;
        private $redis;

        static public function getInstance(){
            if (!self::$_instance){
                self::$_instance=new self();
            }
            return self::$_instance;
        }

        public function __construct(){
            $this->redis = new \Redis();
            $res = $this->redis->connect(config('redis.host'),(int)config('redis.port'),(int)config('redis.time_out'));
            if ($res===false){
                throw new Exception("redis connect error");
            }
        }

        /**
         * @param        $key
         * @param string $value
         * @param int    $time
         * @return bool|string
         */
        public function set( $key,  $value,  $time=0) {
            if (!$key){
                return '';
            }
            if (is_array($value)){
                $value=json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            if (!$time){
                return $this->redis->set($key, $value);
            }
            return $this->redis->setex($key, $time, $value);

        }

        /**
         * @param $key
         * @return bool|string
         */
        public function get($key){
            if (!$key){
                return '';
            }
            return $this->redis->get($key);
        }

        /**
         * 添加有序数列
         * @param $key
         * @param $value
         * @return mixed
         */
        public function sAdd($key, $value){
            return $this->redis->sAdd($key,$value);
        }

        /**
         * 删除序列
         * @param $key
         * @param $member1  要删除的某个序列
         * @return int
         */
        public function sRem($key, $member1){
            return $this->redis->sRem($key, $member1);
        }

        /**
         * 获取所有连接的 id
         * @param $key
         * @return array
         */
        public function sMembers($key){
            return $this->redis->sMembers($key);
        }


    }

