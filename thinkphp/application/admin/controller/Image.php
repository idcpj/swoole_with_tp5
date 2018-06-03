<?php

    namespace app\admin\controller;

    use app\common\lib\Util;
    use think\Controller;
    use think\Request;

    class Image extends Controller{
        const FILE_PATH='thinkphp/public/static/upload';
        const IMG_ROOT='upload';
        public function index(){
            $file = $this->request->file('file');
            $file = $file->isTest(true);
            $info = $file->move(self::FILE_PATH);
            if ($info===false){
                return Util::show($file->getError(), '',config('code.error'));
            }
            $data =[
                'image'=>config('live.host').DS.self::IMG_ROOT.DS.$info->getSaveName(),
            ];

            Util::show("上传成功", $data,config('code.success'));

        }
    }
