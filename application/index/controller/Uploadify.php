<?php
namespace app\index\controller;
use org\Upload;
use think\File;

class Uploadify{
    /**
     * [uploadimg 上传单个图片]
     * @return [type] [description]
     */
    public function uploadimg($file='image'){
        $file = request()->file($file);
        $path = DS .'uploads'. DS .'uploadify'. DS . 'auth';
        $config=[
            'size'=>1024*1024*20,
            'ext'=>'jpg,png,gif'
        ];
        $info = $file->validate($config)->move(ROOT_PATH . 'public'.$path);
        if($info){
            $fullPath =  $path.DS.$info->getSaveName();
            echo $fullPath;
        }else{
            echo $file->getError();
        }
    }
}