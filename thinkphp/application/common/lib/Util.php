<?php

    namespace app\common\lib;
    
    class Util {
        static public function show($msg,$data,$code=1){
            echo json_encode([
                'code' => $code,
                'msg'  => $msg,
                'data' => $data,
            ],JSON_UNESCAPED_UNICODE);
        }
        
    }
