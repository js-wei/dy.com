<?php
namespace app\index\controller;
use think\Validate;

class Publish extends Base{
	
	protected function  _initialize(){
		parent::_initialize();
	}

	public function get_site(){
		$data=[
			'title'=>$this->site['title'],
			'logo'=>$this->site['url'].$this->site['logo'],
			'keywords'=>$this->site['keywords'],
			'description'=>$this->site['description'],
			'url'=>$this->site['url'],
		];
		return ['status'=>1,'data'=>$data];
	}

	public function get_colunm($id=0){
		$data = db('column')->field('id,title,name')->where(['status'=>0,'fid'=>0])->order('sort asc')->select();
		if($id==0){
			$data[0]['active']=1;
		}else{
			foreach ($data as $k => $v) {
				if($v['id']==$id){
					$data[$k]['active']=1;
				}else{
					$data[$k]['active']=0;
				}
			}
		}
		return ['status'=>1,'data'=>$data];
	}





    /**
     * 登陆
     * @param string $phone
     * @param string $password
     * @param string $password_md5
     * @param string $verify
     * @return \think\response\Json
     */
    public function login($phone='',$password=''){

        if(!request()->isPost()){
            return view('login1');
        }
        //逻辑判断
        if(empty($phone)){
            return json(['status'=>0,'msg'=>'请输入您的账号']);
        }
        if(empty($password)){
            return json(['status'=>0,'msg'=>'请输入您的密码']);
        }
        $pwd = substr(md5($password),10,15);
        $where=[
            'phone'=>$phone
        ];
        if(Validate::is($phone,'email')){
            $where=[
                'email'=>$phone
            ];
        }

        $admin = db("member")->field('id,phone,password,nickname,head,email,last_login_time,last_login_ip,status')->where($where)->find();

        if(!$admin){
            return json(['status'=>0,'msg'=>'您的账号输入有误']);
        }
        if($admin['password']!=$pwd){
            return json(['status'=>0,'msg'=>'您的密码输入有误'.$pwd]);
        }
        if($admin['status']==1){
            return json(['status'=>0,'msg'=>'非法操作账号已锁定，请联系管理员解封']);
        }

        //更新登录信息
        $data=array(
            'id'=>$admin['id'],
            'last_login_time'=>time(),
            'last_login_ip'=>request()->ip(),
            'last_login_address'=>$this->get_location()
        );
        db("member")->update($data);
        //保存登录状态
        session('_mid',$admin['id']);

        //跳转目标页
        unset($admin['password']);
        unset($admin['id']);
        unset($admin['status']);
        return json(['status'=>1,'msg'=>'登录成功','data'=>$admin,'redirect'=>Url('/account/information')]);
    }

    /**
     * @author 魏巍
     * @description 发送验证码邮件
     * @param string   $email       邮箱
     * @param int     $uid        用户id
     * @param int      $t           类型:0注册,1找回密码,默认:找回密码
     * @return 返回发送结果
     */
    public function send_email($email='',$uid=0,$t=0){
        if(!request()->isPost()){
            return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if(empty($email)){
            return json(['status'=>0,'msg'=>'请填写邮箱']);
        }
        if(!Validate::is($email,'email')){
            return json(['status'=>0,'msg'=>'抱歉邮箱格式错误']);
        }
        if(cookie('?_session_code')){
            cookie('_session_code',null,time()-60*2);
        }
        $_code = NoRand(0,9,6);
        if(!$t){
            $type = "注册功能";
            cookie($_code.'_session_code',$_code,60*15);
            $html = "【".$this->site['title'].$type."】:"."您在正在使用【".$this->site['title']
                ."】会员账号的".$type.",下面是您的验证码:".$_code.",有效时间为15分钟.如果您没有使用【".$this->site['title']
                ."】的".$type."功能,请自动忽略此邮件谢谢:)";
        }else{
            $type = "找回密码";
            $timespan = base64_encode(time());
            $uid = base64_encode("jswei_".time()."_{$uid}");
            $url = urlencode("{$this->site['url']}/user/find_password?timespan={$timespan}&uid={$uid}&email={$email}");
            $html = "【".$this->site['title'].$type."】:"."您在正在使用【".$this->site['title']
                ."】会员账号的".$type."功能,请点击以下链接进行密码找回".$url.",若没有使用{$type}请自动忽略此邮件谢谢:)";
        }

        if(!think_send_mail($email,$email,"【".$this->site['title']."】".$type,$html)){
            return json(['status'=>0,'msg'=>'验证码发送失败,请稍后重试:(']);
        }
        return json(['status'=>1,'msg'=>'验证码发送成功,请及时查收:)']);
    }
    /**
     * 发送验证码
     * @param string $tel       手机号
     * @param int $type         类型:0通用,1注册,2重置密码
     * @return \think\response\json
     */
    public function send_message($tel='',$type=0){
        if(!$tel){
            return json(['status'=>0,'msg'=>'请输入发送手机号']);
        }

        if(!Validate::is($tel,'/^1[34578]\d{9}$/')){
            return json(['status'=>0,'msg'=>'请输入正确的手机号']);
        }
        if($type==0){
            $arr =send_sms($tel,'111599');
        }else if($type==1){
            $arr =send_sms($tel,'111597');
        }else{
            $arr =send_sms($tel,'111598');
        }
        if (substr($arr,21,6) == 000000) {
            return json(['status'=>1,'msg'=>'验证发送成功']);
        }else{
            return json(['status'=>0,'msg'=>'验证发送失败']);
        }
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
}