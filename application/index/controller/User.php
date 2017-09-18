<?php
namespace app\index\controller;

class User extends Base{
	protected function _initialize(){
		parent::_initialize();
        if(!session('?_mid')){
            $this->redirect('/');
        }
	}
	public function index(){}

    /**
     * 个人信息
     * @return \think\response\View
     */
	public function personal_info(){
	    $id = session('_mid');
	    $userinfo = db('member')->field('password,dates',true)->find($id);
	    $this->assign('info',$userinfo);
		return view();
	}

    /**
     * 选取产品
     * @param int $id
     * @return \think\response\Json
     */
	public function add_product($id=0){
        $mid = session('_mid');
        if(empty($mid)){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($id)){
            return json(['status'=>0,'msg'=>'参数错误']);
        }
        $count = db('my_product')->where('pid','eq',$id)->where('mid','eq',$mid)->count();

        $member = db('member')->field('id,status,is_check')->find($mid);

        if($member['is_check']==0){
            return json(['status'=>0,'msg'=>'您尚未认证不能获取相关产品']);
        }
        if($member['is_check']==2){
            return json(['status'=>0,'msg'=>'您正处于认证审核阶段,请审核通过在选取产品']);
        }
        if(!empty($count)){
            return json(['status'=>0,'msg'=>'您已经获取过此产品,产品只能获取一次']);
        }
        $url = $this->site['url'].'/product/detail.html?id='.$id.'&code='.base64_encode($mid+10000);
        $short_url = json_decode($this->get_short_url($url),true);
        $data = [
            'mid'=>$mid,
            'pid'=>$id,
            'url'=>$url,
            'short_url'=>$short_url[0]['url_short'],
            'date'=>time()
        ];

        if(!db('my_product')->insert($data)){
            return json(['status'=>0,'msg'=>'获取产品失败,请稍后再试']);
        }
        return json(['status'=>1,'msg'=>'获取产品成功,现在就去分享赚钱!','redirect'=>Url('/account/has')]);
    }

    /**
     * 认证
     * @param int $r
     * @return \think\response\View
     */
	public function check($r=0){
        $id = session('_mid');
        $userinfo = db('member')->field('password,dates',true)->find($id);
        $auth = db('authentication')->field('dates',true)->where('mid','eq',$id)->find();

        $this->assign('r',$r);
        $this->assign('auth',$auth);
        $this->assign('info',$userinfo);
        return view();
    }

    /**
     * 申请认证
     * @param string $real_name
     * @param string $idcard_type
     * @param string $card
     * @param string $image
     * @return \think\response\Json
     */
    public function auth($real_name='',$idcard_type='',$card='',$image=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($real_name)){
            return json(['status'=>0,'msg'=>'请输入真实姓名']);
        }
        if(empty($idcard_type)){
            return json(['status'=>0,'msg'=>'请输入证件类型']);
        }
        if(empty($card)){
            return json(['status'=>0,'msg'=>'请输入证件号']);
        }
        if(empty($image)){
            return json(['status'=>0,'msg'=>'请输入证件照']);
        }

        $data = request()->param();
        if(!empty($data['_id'])){
            $auth = db('authentication')->find($data['_id']);
            if(!unlink('.'.$auth['image'])){
                return json(['status'=>0,'msg'=>'删除原证件照失败,请稍后再试']);
            }
        }
        unset($data['_id']);
        $data['date']=time();
        if(!db('authentication')->insert($data)){
            return json(['status'=>0,'msg'=>'操作失败,请稍后再试']);
        }
        db('member')->update([
            'id'=>$id,
            'is_check'=>2,
            'date'=>time()
        ]);
        return json(['status'=>1,'msg'=>'操作成功,等待审核']);
    }

    /**
     * 删除
     * @param int $id
     * @return \think\response\Json
     */
    public function delete($id=0){
        if(!db('message')->delete($id)){
            return json(['status'=>0,'msg'=>'删除失败']);
        }
        return json(['status'=>1,'msg'=>'删除成功']);
    }

    /**
     * 删除所有
     * @param string $id
     * @return \think\response\Json
     */
    public function delete_all($id=''){
        if(empty($id)){
            return json(['status'=>0,'msg'=>'参数错误']);
        }
        if(!db('message')->where('id','in',$id)->delete()){
            return json(['status'=>0,'msg'=>'删除失败']);
        }
        return json(['status'=>1,'msg'=>'删除成功']);
    }

    /**
     * 消息
     * @param int $id
     * @param int $status
     * @return \think\response\Json|\think\response\View
     */
    public function message($id=0,$status=-1){
        $tl='';
        if($id){
            $vo = db('message')->find($id);
            $this->assign('vo',$vo);
            $tl='detail';
            db('message')->update([
               'id'=>$id,
               'status'=>1,
               'date'=>time()
            ]);
        }else{
            $where=[];
            if($status>-1){
                $where['status']=$status;
            }
            $list = db('message')->where($where)->order('date desc')->paginate(8);
            $this->assign('list',$list);
            $this->assign('status',$status);
        }
        return view($tl);
    }

    /**
     * 修改信息
     * @param string $t
     * @return \think\response\View
     */
	public function alter($t='phone'){
	    switch ($t){
            case 'phone':
                $tpl='phone';
                break;
            case 'email':
                $tpl='email';
                break;
            case 'alipay':
                $tpl='alipay';
                break;
            case 'bank':
                $tpl='bank';
                break;
            case 'city':
                $tpl='city';
                break;
            case 'address':
                $tpl='address';
                break;
            case 'zipcode':
                $tpl='zipcode';
                break;
            default:
                $tpl='phone';
        }
        return  view($tpl);
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
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }

		if(empty($email)){
			return json(['status'=>0,'msg'=>'请输入邮箱']);
		}
		if(empty($verify)){
			return json(['status'=>0,'msg'=>'请输入验证码']);
		}
		if(!cookie('?'.$verify.'_session_code')){
			return json(['status'=>0,'msg'=>'验证码已失效']);
		}
		if($verify!=cookie($verify.'_session_code')){
			return json(['status'=>0,'msg'=>'请输入正确的验证码']);
		}
        $member = db('member')->field('id')->find($id);
        if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }

        if(!db('member')->update([
			'id'=>$id,
			'email'=>$email,
			'date'=>time()
		])){
			return json(['status'=>0,'msg'=>'操作失败,请重试']);
		}
		return json(['status'=>1,'msg'=>'操作成功']);
	}

    /**
     * 设置支付宝
     * @param string $alipay
     * @return \think\response\Json
     */
	public function alipay($alipay=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($alipay)){
            return json(['status'=>0,'msg'=>'请输入支付宝账号']);
        }
        $member = db('member')->field('id')->find($id);
        if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }
        if(!db('member')->update([
            'id'=>$id,
            'alipay'=>$alipay,
            'date'=>time()
        ])){
            return json(['status'=>0,'msg'=>'操作失败,请重试']);
        }
        return json(['status'=>1,'msg'=>'操作成功']);

    }

    /**
     * 银行卡号
     * @param string $bank_name
     * @param string $bank_no
     * @param string $bank_people
     * @return \think\response\Json
     */
    public function bank($bank_name='',$bank_no='',$bank_people=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($bank_name)){
            return json(['status'=>0,'msg'=>'请输入开户银行']);
        }
        if(empty($bank_people)){
            return json(['status'=>0,'msg'=>'请输入开户人']);
        }

        if(empty($bank_no)){
            return json(['status'=>0,'msg'=>'请输入银行卡号']);
        }

        $member = db('member')->field('id')->find($id);
        if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }
        if(!db('member')->update([
            'id'=>$id,
            'bank_name'=>$bank_name,
            'bank_no'=>$bank_no,
            'bank_people'=>$bank_people,
            'date'=>time()
        ])){
            return json(['status'=>0,'msg'=>'操作失败,请重试']);
        }
        return json(['status'=>1,'msg'=>'操作成功']);
    }

    /**
     * 修改城市
     * @param string $city
     * @return \think\response\Json
     */
    public function city($city=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($city)){
            return json(['status'=>0,'msg'=>'请输入所在城市']);
        }

        $member = db('member')->field('id')->find($id);
        if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }
        if(!db('member')->update([
            'id'=>$id,
            'city'=>$city,
            'date'=>time()
        ])){
            return json(['status'=>0,'msg'=>'操作失败,请重试']);
        }
        return json(['status'=>1,'msg'=>'操作成功']);
    }

    /**
     * 详细地址
     * @param string $address
     * @return \think\response\Json
     */
    public function address($address=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($address)){
            return json(['status'=>0,'msg'=>'请输入详细地址']);
        }

        $member = db('member')->field('id')->find($id);
        if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }
        if(!db('member')->update([
            'id'=>$id,
            'address'=>$address,
            'date'=>time()
        ])){
            return json(['status'=>0,'msg'=>'操作失败,请重试']);
        }
        return json(['status'=>1,'msg'=>'操作成功']);
    }

    /**
     * 设置邮编
     * @param string $zip_code
     * @return \think\response\Json
     */
    public function zip_code($zip_code=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
        if(empty($zip_code)){
            return json(['status'=>0,'msg'=>'请输入邮编']);
        }
        $member = db('member')->field('id')->find($id);
        if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }
        if(!db('member')->update([
            'id'=>$id,
            'zip_code'=>$zip_code,
            'date'=>time()
        ])){
            return json(['status'=>0,'msg'=>'操作失败,请重试']);
        }
        return json(['status'=>1,'msg'=>'操作成功']);
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
        $d = cookie($verify.'_session_code');
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
	 * @param string $phone 手机
	 * @param string $verify 验证码
	 */
	public function setTel($phone='',$verify=''){
        $id = session('_mid');
        if(!$id){
            return json(['status'=>0,'msg'=>'没有登录']);
        }
		if(empty($phone)){
			return json(['status'=>0,'msg'=>'请输入邮箱']);
		}
		if(empty($verify)){
			return json(['status'=>0,'msg'=>'请输入验证码']);
		}

		if(!cookie('?'.$verify.'_session_code')){
			return json(['status'=>0,'msg'=>'验证码已失效']);
		}

		if($verify!=cookie($verify.'_session_code')){
			return json(['status'=>0,'msg'=>'请输入正确的验证码']);
		}

        $member = db('member')->field('id')->find($id);
		if(empty($member)){
            return json(['status'=>0,'msg'=>'没有此账号']);
        }

		if(!db('member')->update([
			'id'=>$member['id'],
			'phone'=>$phone,
			'date'=>time()
		])){
			return json(['status'=>0,'msg'=>'修改失败,请重试']);
		}
		return json(['status'=>1,'msg'=>'修改成功']);
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