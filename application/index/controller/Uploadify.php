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

    public function webUploader($file='file'){
        $file = request()->file($file);
        $path = DS .'uploads'. DS .'uploadify'. DS . 'auth';
        $config=[
            'size'=>1024*1024*20,
            'ext'=>'jpg,png,gif'
        ];
        $info = $file->validate($config)->move(ROOT_PATH . 'public'.$path);

        if($info){
            $fullPath =  $path.DS.$info->getSaveName();
            //{"jsonrpc" : "2.0", "result" : null, "id" : "id"}
            return json([
                "jsonrpc" => "2.0",
                'result'=>[
                    'code'=>200,
                    'file'=>$fullPath,
                    'id'=>'id'
                ]
            ]);
        }else{
            return json([
                "jsonrpc" => "2.0",
                'error'=>[
                    'code'=>102,
                    'message'=>"Failed to open output stream.",
                    'id'=>'id'
                ]
            ]);
        }
    }
}