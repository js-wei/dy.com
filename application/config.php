<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-07-31T16:54:43+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-22T11:49:58+08:00



// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'app_debug'              => true,
    // 默认模块名
    'default_module'        => 'admin',
    // 默认控制器名
    'default_controller'    => 'index',
    // 默认操作名
    'default_action'        => 'index',
    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 25,
        // 是否画混淆曲线
        'useCurve' => false,
         // 验证码图片高度
        'imageH'   => 50,
        // 验证码图片宽度
        'imageW'   => 220,
        // 验证码位数
        'length'   => 5,
        // 验证成功后是否重置
        'reset'    => true
    ],
    'session'                => [
        'prefix'         => 'think',
        'type'           => '',
        'auto_start'     => true,
    ],
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__'=>'/static',
        '__ROOT__'=>'/',
        '__PLUG__'=>'/static/plug',
        '__JS__'=>'/static/index/js',
        '__CSS__'=>'/static/index/css'
    ],
    'template'      => [
        // 模板引擎
        'type'   => 'think',
        //标签库标签开始标签
        'taglib_begin'  =>  '<',
        //标签库标签结束标记
        'taglib_end'    =>  '>',
        // 预先加载的标签库
        //'taglib_pre_load'     => 'app\common\taglib\Article',
        'taglib_build_in'    =>    'cx,app\common\taglib\Article',
    ],
    'THINK_EMAIL'=>[       //邮件发送
        'SMTP_HOST'=>'smtp.163.com',
        'SMTP_PORT'=>25,
        'SMTP_USER'=>'jswei30@163.com',
        'SMTP_PASS'=>'jswei30',
        'FROM_EMAIL'=>'jswei30@163.com',
        'FROM_NAME'=>'官方邮件',
        'REPLY_EMAIL'=>'',
        'REPLY_NAME'=>''
    ],
    //云之讯短信接口
    'Ucpaas'=>[
        'accountSid'=>'73ba580fb6aae884362e1ac7c9fc46b2',
        'authToken'=>'b7e16a1d2fe0cf267b0803f82813d621',
        'appId'=>'7ea4faede77749e995fd9ed9abe4d132',
    ],
    'AMAP'=>[
        'KEY'=>'5c34d8399b8beffa18d9b98731385bf3',
        'SECRET'=>'b2a743e44f477fef25fca9596f193402'
    ],
    'GeTui'=>[
        'AppID'=>'8lrYkxNeKS7Es2yg4QAFF8',
        'AppSecret'=>'u6yYf21r7mAzWvqM9I5rFA',
        'AppKey'=>'rjw33LZqHv84mkK3ojx4R6',
        'MasterSecret'=>'dPSlFDgKmB8FOoLOLNfGA1'
    ],
    'UPLOAD'=>[
        'UPLOAD_PATH'=>ROOT_PATH . 'public' . DS.'uploads',
        'UPLOAD_IMAGE'=>[
            'size'=>1024*1024*5,        //5M最大
            'ext'=>'jpg,png,gif,bmp,webp'
        ],
        'UPLOAD_FILE'=>[
            'size'=>1024*1024*8,        //8M最大
            'ext'=>'txt,zip,tar,xls,pdf,doc,docx,rar,xlsx'
        ],
        'UPLOAD_EDITOR'=>[
            'size'=>1024*1024*8,        //8M最大
            'ext'=>'jpg,png,gif,txt,zip,rar,tar,xls,pdf,doc,docx,xlsx'
        ],
    ],
    'speech'=>[
        'appid'=>'7312072',
        'apikey'=>'7zk7tQsS5hvq9Lspm5AxEwvU',
        'secretkey'=>'6eafa685183bddd79e175c924ceee751',
        'path'=>  '.'.DS.'data'. DS .'speech',
        'expires_in'=>3600,
    ],
    'ENCRYPT_KEY'=>'THINK',         //加密key
    'LOG_PATH'=>'/log',
];
