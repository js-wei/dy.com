<?php
namespace app\index\controller;
use QL\QueryList;
use GuzzleHttp;

class Index extends Base{
    public function index(){
	    return view();
    }
    public function login(){
        return view();
    }

    public function jsapi(){
        $tools = new \service\Wechat('wxf02790fbcadf974a','d5f062346b24ca499e6997fc2f38d4db','');
        $options = $tools->getSignPackage();
        $this->assign('options',$options);
        return view();
    }

    /**
     * 检测用户
     * @param $phone
     * @return bool
     */
    public function check_phone($phone){
        $i = db('member')->where('phone','eq',$phone)->count();
        return $i?false:true;
    }

    public function signup($phone='',$verify='',$password='',$confirm_password=''){
        if(request()->IsGet()){
            return view();
        }

        if(empty($phone)){
            return json([
                'status'=>0,
                'msg'=>'请输入手机号'
            ]);
        }
        if(empty($verify)){
            return json([
                'status'=>0,
                'msg'=>'请输入验证码'
            ]);
        }
        if(empty($password)){
            return json([
                'status'=>0,
                'msg'=>'请输入密码'
            ]);
        }
        if(empty($confirm_password)){
            return json([
                'status'=>0,
                'msg'=>'请输入确认密码'
            ]);
        }
        if($verify!=cookie($verify.'_session_code')){
            return json(['status'=>0,'msg'=>'验证码错误']);
        }
        if($confirm_password!=$password){
            return json([
                'status'=>0,
                'msg'=>'输入的密码不一致'
            ]);
        }
        $param = request()->param();
        $param['date']=time();
        unset($param['confirm_password']);
        unset($param['verify']);
        $param['password'] = substr(md5($password),10,15);

        if(!db('member')->insert($param)){
            return json([
                'status'=>0,
                'msg'=>'抱歉注册失败,请稍后再试'
            ]);
        }
        return json([
            'status'=>1,
            'msg'=>'恭喜你注册成功,是否登录管理后台!',
            'redirect'=>Url('/')
        ]);
    }
}