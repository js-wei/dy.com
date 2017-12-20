<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:37:51+08:00



namespace app\index\controller;

use org\Upload;
use think\File;

class Uploadify
{
    /**
     * 上传头像
     * @param string $file
     * @param array $crop
     * @param int $quality
     * @return array|string
     */
    public function upload_head($file='image', $crop=[], $quality=50)
    {
        $file = request()->file($file);
        $path = config('UPLOAD.UPLOAD_PATH'). DS .'head';
        //$valid = config('UPLOAD.UPLOAD_IMAGE');
        $ymd = date('Ymd',time());
        $info = $file->move($path, $ymd. DS .md5(build_order_no()).'.png');
        if ($info) {
            //$save_name =
            $_path = DS . 'public' .  DS .'uploads' . DS .'head';
            $this->_image_worker($path . DS . $info->getSaveName(), $crop, $quality);
            return [
                'fullpath'=>str_replace('/public',' ',$_path). DS .$info->getSaveName(),
                'filename'=>$info->getSaveName()
            ];
        } else {
            return $file->getError();
        }
    }


    /**
     * 上传头像
     * @param string $file
     * @return \think\response\Json
     */
    public function webUploader($file='file')
    {
        $file = request()->file($file);
        $path = DS .'uploads'. DS .'uploadify'. DS . 'auth';
        $config=[
            'size'=>1024*1024*20,
            'ext'=>'jpg,png,gif'
        ];
        $info = $file->validate($config)->move(ROOT_PATH . 'public'.$path);

        if ($info) {
            $fullPath =  $path.DS.$info->getSaveName();
            return json([
                "jsonrpc" => "2.0",
                'result'=>[
                    'code'=>200,
                    'file'=>$fullPath,
                    'id'=>'id'
                ]
            ]);
        } else {
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

    /**
     * 裁切
     * @param $path
     * @param $crop
     * @param int $quality
     */
    protected function _image_worker($path, $crop, $quality=80)
    {
        $image = \think\Image::open($path);
        if ($crop) {
            $image->crop($crop['width'], $crop['height'], $crop['x'], $crop['y'])
                ->save($path, null, $quality);
        } else {
            $image->save($path, null, $quality);
        }
    }
}
