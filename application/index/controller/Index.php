<?php
namespace app\index\controller;
use QL\QueryList;
use GuzzleHttp;

class Index extends Base{
    public function index(){
	    return view();
    }

    public function register($nickname='',$city='',$address='',$phone='',$email='',$password='',$repeat_password='',$alipay=''){
        if(!request()->isPost()){
            return json([
               'status'=>0,
               'msg'=>'错误的请求方式'
            ]);
        }

        if(empty($nickname)){
            return json([
                'status'=>0,
                'msg'=>'姓名不能为空'
            ]);
        }
        if(empty($city)){
            return json([
                'status'=>0,
                'msg'=>'城市不能为空'
            ]);
        }
        if(empty($address)){
            return json([
                'status'=>0,
                'msg'=>'详细地址不能为空'
            ]);
        }
        if(empty($phone)){
            return json([
                'status'=>0,
                'msg'=>'手机号不能为空'
            ]);
        }
        if(empty($email)){
            return json([
                'status'=>0,
                'msg'=>'邮箱不能为空'
            ]);
        }
        if(empty($password)){
            return json([
                'status'=>0,
                'msg'=>'密码不能为空'
            ]);
        }
        if(empty($repeat_password)){
            return json([
                'status'=>0,
                'msg'=>'确认密码不能为空'
            ]);
        }
        if(empty($alipay)){
            return json([
                'status'=>0,
                'msg'=>'支付宝不能为空'
            ]);
        }
        $param = request()->param();
        $param['date']=time();
        unset($param['repeat_password']);

        if(!db('member')->insert($param)){
            return json([
                'status'=>0,
                'msg'=>'抱歉注册失败,请稍后再试'
            ]);
        }
        return json([
           'status'=>1,
           'msg'=>'恭喜你注册成功,是否登录管理后台!',
           'redirect'=>Url('/admin')
        ]);
    }
}