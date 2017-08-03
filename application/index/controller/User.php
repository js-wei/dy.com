<?php
namespace app\index\controller;

class User extends Base{
	protected function _initialize(){
		parent::_initialize();
        if(!session('?_mid')){
            $this->redirect('/');
        }
	}
	public function personal_info(){
	    $id = session('_mid');
	    $userinfo = db('member')->field('password,dates',true)->find($id);
	    $this->assign('info',$userinfo);
		return view();
	}

	/**
	 * @author 魏巍
	 * @descriptionn 检测用户是否存在
	 * @param string $username  用户名
	 */
	public function check_username($username=''){
		if(empty($username)){
			return jsonp(['status'=>0,'msg'=>'请输入用户名']);
		}
		$member = db('member')->where(['username'=>$username])->find();
		if(empty($member)){
			return jsonp(['status'=>1,'msg'=>'恭喜您用户名未被注册可以使用']);
		}else{
			return jsonp(['status'=>0,'msg'=>'抱歉用户名已被占用不可使用']);
		}
	}
	/**
	 * @author 魏巍
	 * @descriptionn 检昵称是否存在
	 * @param $nickname string 昵称
	 */
	public function check_nickname($nickname=''){
		if(empty($nickname)){
			return jsonp(['status'=>0,'msg'=>'请输入要修改的昵称']);
		}
		$member = db('member')->where(['nickname'=>$nickname])->find();
		if(empty($member)){
			return jsonp(['status'=>1,'msg'=>'恭喜您昵称未被注册可以使用']);
		}else{
			return jsonp(['status'=>0,'msg'=>'抱歉昵称已被占用不可使用']);
		}
	}

	/**
	 * 修改昵称
	 * @param string $nickname 昵称
	 * @param string $verify 验证码
	 */
	public function update_nickname($nickname='',$verify=''){
		if(empty($verify)){
			return jsonp(['status'=>0,'msg'=>'请填写验证码']);
		}
		if(empty($nickname)){
			return jsonp(['status'=>0,'msg'=>'请填写昵称']);
		}
		if(!captcha_check($verify)){
			return jsonp(['status'=>0,'msg'=>'验证码错误']);
		}
		if(!db('member')->update([
			'id'=>session('_id'),
			'nickname'=>$nickname,
			'date'=>time()
		])){
			return jsonp(['status'=>0,'msg'=>'昵称修改失败,请稍后重试']);
		}
		session('_nickname',$nickname);
		return jsonp(['status'=>1,'msg'=>'修改成功','redirect'=>Url('user/userinfo')]);
	}

	/**
	 * @param int $id
	 * @param int $pwd
	 * @param string $pwd
	 * @param string $verify
	 */
   public function set_password($id=0,$phone=0,$old_password='',$new_password='',$comfr_password='',$verify=''){
	   if(!captcha_check($verify)){
		    return jsonp(['status'=>0,'msg'=>'请填写正确的验证码']);
	   }
	   if(empty($old_password)){
		    return jsonp(['status'=>0,'msg'=>'原始密码不能为空']);
	   }
	   if(empty($new_password)){
		    return jsonp(['status'=>0,'msg'=>'新密码不能为空']);
	   }
	   if(empty($comfr_password)){
		    return jsonp(['status'=>0,'msg'=>'确认密码不能为空']);
	   }
       if($id){
           $admin = db('member')->find($id);
       }else{
           $admin = db('member')->where('phone','eq',$phone)->find($id);
       }

	   if($admin['password']!=substr(md5(strtolower($old_password)),5,10)){
		    return jsonp(['status'=>0,'msg'=>'原始密码不正确']);
	   }

	   if(!db('member')->update([
		   'id'=>$id,
		   'password'=>substr(md5(strtolower($new_password)),5,10),
		   'date'=>time()
	   ])){
		   return jsonp(['status'=>0,'msg'=>'修改密码失败,请稍后重试']);
	   }
	   return jsonp(['status'=>1,'msg'=>'密修改成功']);
   }

	/**
	 * @author 魏巍
	 * @description 设置邮箱
	 * @param string $email 邮箱
	 * @param string $verify 验证码
	 */
	public function setEmail($email='',$verify=''){
		if(empty($email)){
			return jsonp(['status'=>0,'msg'=>'请输入邮箱']);
		}
		if(empty($verify)){
			return jsonp(['status'=>0,'msg'=>'请输入验证码']);
		}
		if(cookie('?_session_code')){
			return jsonp(['status'=>0,'msg'=>'验证码已失效']);
		}
		if($verify!=cookie('_session_code')){
			return jsonp(['status'=>0,'msg'=>'请输入正确的验证码']);
		}
		if(!db('member')->save([
			'id'=>session('_id'),
			'email'=>$email,
			'date'=>time()
		])){
			return jsonp(['status'=>0,'msg'=>'修改失败,请重试']);
		}
		return jsonp(['status'=>1,'msg'=>'修改成功','redirect'=>U('user/userInfo')]);
	}

    /**
     * 验证验证码
     * @param string $verify         验证码
     * @return \think\response\json
     */
    public function check_sms($verify=''){
        if(empty($verify)){
            return json(['status'=>0,'msg'=>'请输入验证码']);
        }
        $d = cookie('?'.$verify.'_session_code');
        if(empty(cookie('?'.$verify.'_session_code'))){
            return json(['status'=>0,'msg'=>'验证码已失效']);
        }
        if($verify!=$d){
            return json(['status'=>0,'msg'=>'验证码错误']);
        }
        return json(['status'=>1,'msg'=>'验证码输入正确']);
    }

	/**
	 * @author 魏巍
	 * @description 设置手机
	 * @param string $tel 手机
	 * @param string $verify 验证码
	 */
	public function setTel($tel='',$verify=''){
		if(empty($tel)){
			return jsonp(['status'=>0,'msg'=>'请输入邮箱']);
		}
		if(empty($verify)){
			return jsonp(['status'=>0,'msg'=>'请输入验证码']);
		}
		if(cookie('?_session_code')){
			return jsonp(['status'=>0,'msg'=>'验证码已失效']);
		}
		if($verify!=cookie('_session_code')){
			return jsonp(['status'=>0,'msg'=>'请输入正确的验证码']);
		}
		if(!db('member')->save([
			'id'=>session('_id'),
			'email'=>$tel,
			'date'=>time()
		])){
			return jsonp(['status'=>0,'msg'=>'修改失败,请重试']);
		}
		return jsonp(['status'=>1,'msg'=>'修改成功','redirect'=>Url('user/userInfo')]);
	}




	/**
	 * @author 魏巍
	 * @description		忘记密码找回
	 * @param string $username 	用户名
	 * @param string $email 	安全邮箱
	 * @param string $verify	验证码
	 */
	public function forget_handler($password='',$comfr_password='',$verify=''){
		if(empty($password)){
			return jsonp(['status'=>0,'msg'=>'请输入密码']);
		}
		if(empty($verify)){
			return jsonp(['status'=>0,'msg'=>'请输入验证码']);
		}
		$_verify = cookie($verify.'_session_code');
		if(empty($_verify)){
			return jsonp(['status'=>0,'msg'=>'验证码已失效']);
		}
		if($comfr_password!=$password){
			return jsonp(['status'=>0,'msg'=>'两次输入的密码不一致']);
		}
		if($_verify!=$verify){
			return jsonp(['status'=>0,'msg'=>'验证码错误']);
		}
		$member = db('member')->field('date',true)->find(session('_id'));
		if(empty($member)){
			return jsonp(['status'=>0,'msg'=>'用户名不存在,请填写正确的用户名']);
		}
		if(!db('member')->save([
			'id'=>$member['id'],
			'password'=>substr(md5($password),10,10),
			'dates'=>time()
		])){
			return jsonp(['status'=>0,'msg'=>'修改失败请重试']);
		}
		cookie($verify.'_session_code',time()-60*2);
		return jsonp(['status'=>1,'msg'=>'修改成功,请登录','redirect'=>Url('duobao/login')]);
	}

	public function find_password($phone='',$password='',$confirm_password=''){
        if(empty($phone)){
            return json(['status'=>0,'msg'=>'参数错误']);
        }
        if(empty($password)){
            return json(['status'=>0,'msg'=>'请输入密码']);
        }
        if(empty($confirm_password)){
            return json(['status'=>0,'msg'=>'请输入确认密码']);
        }
        if($confirm_password!=$password){
            return json(['status'=>0,'msg'=>'两次密码不一致']);
        }
        $member = db('member')->field('date',true)->where('phone','eq',$phone)->find();
        if(empty($member)){
            return json(['status'=>0,'msg'=>'用户名不存在,请填写正确的用户名']);
        }
        if(!db('member')->update([
            'id'=>$member['id'],
            'password'=>substr(md5($password),10,15),
            'dates'=>time()
        ])){
            return json(['status'=>0,'msg'=>'修改失败请重试']);
        }
        return json(['status'=>1,'msg'=>'修改成功,请登录','redirect'=>Url('/user/login')]);
    }


	/**
	 * @author 魏巍
	 * @description		忘记密码找回处理函数
	 * @param string $email 	安全邮箱
	 * @param int 	$timespan	安全时间戳
	 * @param string $ucion 	用户主键
	 */
	public function find_passwords($email='',$timespan=0,$ucion=''){
		$_result = '';
		if(empty($email)){
			return jsonp(['status'=>0,'msg'=>'安全邮箱为空']);
		}
		if(empty($timespan)){
			return jsonp(['status'=>0,'msg'=>'时间戳为空']);
		}

		$timespan = base64_decode($timespan);
		$_timespan = time_diff($timespan, time());

		if($_timespan['min']>30){
			return jsonp(['status'=>0,'msg'=>'链接已经失效']);
		}

		$uid = explode('_', base64_decode($ucion))[2];
		$member = db('member')->field('date,password',true)->where(['id'=>$uid,'email'=>$email])->find();
		if(empty($member)){
			return jsonp(['status'=>0,'msg'=>'查无此用户']);
		}
		if(empty($_result) && $member['email']!=$email){
			return jsonp(['status'=>0,'msg'=>'安全邮箱错误']);
		}
		return jsonp(['status'=>1,'msg'=>'邮件发送成功']);
	}
}