<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2017/11/6
 * Time: 15:08
 */

namespace app\index\controller;
use think\Validate;
use think\Session;

class Api extends Base{

    protected function  _initialize(){
        parent::_initialize();
    }

	public function personal_info($id=0){
		if(!request()->isPost()){
			return json(['status'=>0,'msg'=>'错误的请求方式']);
		}
	    $_id = $id?$id:session('_mid');
		if(!$_id){
			return json(['status'=>0,'msg'=>'缺少必要的条件']);
		}
	    $userinfo = db('member')->field('password,openid,last_login_time,last_login_address,last_login_ip,status,dates',true)->find($_id);
	    if(!$userinfo){
	    	return json(['status'=>0,'msg'=>'查询失败']);
	    }
		return json(['status'=>1,'msg'=>'查询成功','data'=>$userinfo]);
	}

    /**
     * 检昵称是否存在
     * @param string $nickname
     * @return \think\response\Json
     */
	public function check_nickname($nickname=''){
		if(empty($nickname)){
			return json(['status'=>0,'msg'=>'请输入要修改的昵称']);
		}
		$member = db('member')->where(['nickname'=>$nickname])->find();
		if(empty($member)){
			return json(['status'=>1,'msg'=>'恭喜您昵称未被注册可以使用']);
		}else{
			return json(['status'=>0,'msg'=>'抱歉昵称已被占用不可使用']);
		}
	}

    public function get_site(){
        $data=[
            'title'=>$this->site['title'],
            'logo'=>str_replace('//', '/', $this->site['url'].$this->site['logo']),
            'keywords'=>$this->site['keywords'],
            'description'=>$this->site['description'],
            'url'=>$this->site['url'],
        ];
        return ['status'=>1,'data'=>$data];
    }

    public function get_column($id=0){
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
     * 用户登陆
     * @param string $phone
     * @param string $password
     * @param int $type
     * @return \think\response\Json|\think\response\View
     */
    public function login($phone='',$password='',$type=0){
        if(!request()->isPost()){
            return json(['status'=>0,'msg'=>'错误的请求方式']);
        }
        //逻辑判断
        if(empty($phone)){
            return json(['status'=>0,'msg'=>'请输入您的账号']);
        }
        if(empty($password)){
            return json(['status'=>0,'msg'=>'请输入您的密码']);
        }

        if(!$type){     //账号登录
            $where=[
                'phone'=>$phone
            ];
            if(Validate::is($phone,'email')){
                $where=[
                    'email'=>$phone
                ];
            }
            $admin = db("member")
                ->field('id,phone,password,nickname,head,email,last_login_time,last_login_ip,status')
                ->where($where)->find();
            if(!$admin){
                return json(['status'=>0,'msg'=>'您的账号输入有误']);
            }

            if($admin['password']!=$this->get_password($password)){
                return json(['status'=>0,'msg'=>'您的密码输入有误']);
            }
        }else{
            $flag = $this->check_verify($password,true);  //验证码验证
            if(!$flag['status']){
                return $flag;
            }
            $where=[
                'phone'=>$phone
            ];
            $admin = db("member")
                ->field('id,phone,password,nickname,head,email,last_login_time,last_login_ip,status')
                ->where($where)->find();
            if(!$admin){
                return json(['status'=>0,'msg'=>'您的账号输入有误']);
            }
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
        session('_m',$admin['phone']);
        //跳转目标页
        unset($admin['password']);
        unset($admin['id']);
        unset($admin['status']);
        return json(['status'=>1,'msg'=>'登录成功','data'=>$admin]);
    }

    /**
     * 用户注册
     * @param string $phone
     * @param string $password
     * @param string $verify
     * @return \think\response\Json|\think\response\View
     */
    public function register($phone='',$password='',$verify=''){
        if(!request()->isPost()){
            //return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if(empty($verify)){
            return json(['status'=>0,'msg'=>'请输入验证码']);
        }
        if(empty($phone)){
            return json(['status'=>0,'msg'=>'请输入手机号']);
        }
        if(empty($password)){
            return json(['status'=>0,'msg'=>'请输入您的密码']);
        }
        $flag = $this->check_verify($verify,true);  //验证码验证
        if(!$flag['status']){
            return $flag;
        }
        $member = [
            'phone'=>$phone,
            'password'=>$this->get_password($password),
            'tel'=>$phone,
            'date'=>time()
        ];
        $admin = db('member')->where('phone','eq',$phone)->find();

        if($admin){
            return json(['status'=>0,'msg'=>'用户已存在']);
        }
        if(!db('member')->insert($member)){
            return json(['status'=>0,'msg'=>'注册失败']);
        }
        $id = db('member')->getLastInsID();
        //保存登录状态
        return json(['status'=>1,'msg'=>'恭喜您注册成功','last_id'=>$id]);
    }

    /**
     * 找回密码
     * @param string $phone
     * @param string $password
     * @param string $confirm_password
     * @return \think\response\Json
     */
    public function set_password($phone='',$password='',$confirm_password=''){
        if(!request()->isPost()){
            return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if(empty($phone)){
            return json(['status'=>0,'msg'=>'参数错误',]);
        }
        if(empty($password)){
            return json(['status'=>0,'msg'=>'请输入新密码']);
        }
        if(empty($confirm_password)){
            return json(['status'=>0,'msg'=>'请输入确认密码']);
        }
        if($confirm_password!=$password){
            return json(['status'=>0,'msg'=>'两次密码不一致']);
        }
        $member = db('member')->field('id')->field('date',true)->where('phone','eq',$phone)->find();
        if(empty($member)){
            return json(['status'=>0,'msg'=>'用户名不存在']);
        }
        if(!db('member')->update([
            'id'=>$member['id'],
            'password'=>$this->get_password($password),
            'dates'=>time()
        ])){
            return json(['status'=>0,'msg'=>'修改失败请重试']);
        }
        return json(['status'=>1,'msg'=>'修改成功']);
    }

    /**
     * 用户退出
     * @return array
     */
    public function logout(){
        Session::delete('_mid');
        Session::delete('_m');
        return array('status'=>1,'msg'=>'退出成功');
    }

    /**
     * @author 魏巍
     * @description 发送验证码邮件
     * @param string   $email       邮箱
     * @return 返回发送结果
     */
    public function send_email_code($email=''){
        if(!request()->isPost()){
            //return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if(empty($email)){
            return json(['status'=>0,'msg'=>'请填写邮箱']);
        }
        if(!Validate::is($email,'email')){
            return json(['status'=>0,'msg'=>'抱歉邮箱格式错误']);
        }
        $_code = NoRand(0,9,6);
        cookie($_code.'_session_code',$_code,60*15);
        $html = "【".$this->site['title']."】:您本次的验证码:".$_code.",有效时间为15分钟.如果您没有使用【".$this->site['title']
            ."】相关产品,请自动忽略此邮件谢谢:)";

        if(!think_send_mail($email,$email,"【".$this->site['title']."】",$html)){
            return json(['status'=>0,'msg'=>'验证码发送失败,请稍后重试:(']);
        }
        return json(['status'=>1,'msg'=>'验证码发送成功到邮箱['.$email.']中,请及时查收:)']);
    }
    /**
     * 发送验证码
     * @param string $tel       手机号
     * @param int $type         类型:0通用,1注册,2重置密码
     * @return \think\response\json
     */
    public function send_message($tel='',$type=0){
    	if(!request()->isPost()){
            return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if(!$tel){
            return json(['status'=>0,'msg'=>'请输入发送手机号']);
        }
        if(!Validate::is($tel,'/^1[34578]\d{9}$/')){
            return json(['status'=>0,'msg'=>'请输入正确的手机号']);
        }

        if($type==0){
            $arr = send_sms($tel,'227478');
        }else if($type==1){
            $arr = send_sms($tel,'227475');
        }else{
            $arr = send_sms($tel,'227477');
        }
		
        if (substr($arr,21,6) == 000000) {
            return json(['status'=>1,'msg'=>'验证成功下发,请注意查收']);
        }else{
            return json(['status'=>0,'msg'=>'验证下发失败']);
        }
    }

    /**
     * 验证验证码
     * @param string $verify
     * @return array
     */
    public function check_code($verify=''){
        if(!$verify){
            return ['status'=>0,'msg'=>'请输入验证码'];
        }
        $_result = $this->check_verify($verify);
        return $_result;
    }

    /**
     * 修改用户昵称
     * @param int $uid
     * @param string $nickname
     * @return array
     */
    public function upgrade_nickname($uid=0,$nickname=''){
        if(!request()->isPost()){
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if(!$uid){
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        if(!$nickname){
            return ['status'=>0,'msg'=>'缺少必要参数nickname'];
        }
        $member = db('member')->field('id,nickname,phone')->find($uid);
        if(!$member){
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        $count = db('member')->where('nickname','like','%'.$nickname.'%')->count('*');
        if($count){
            return ['status'=>0,'msg'=>'用户昵称已存在'];
        }
        if(has_chiness($nickname) && mb_strlen($nickname)>30){
            return ['status'=>0,'msg'=>'用户昵称过长'];
        }
        if(!has_chiness($nickname) && mb_strlen($nickname)>10){

            return ['status'=>0,'msg'=>'用户昵称过长'];
        }
        if(!db('member')->update([
            'id'=>$member['id'],
            'nickname'=>$nickname,
            'dates'=>time()
        ])){
            return ['status'=>0,'msg'=>'用户昵称修改失败'];
        }
        return ['status'=>1,'msg'=>'用户昵称修改成功','nickname'=>$nickname];
    }

    /**
     * 修改性别
     * @param int $uid
     * @param int $sex
     * @return array
     */
    public function upgrade_sex($uid=0,$sex=0){
        if(!request()->isPost()){
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if(!$uid){
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        $member = db('member')->field('id,nickname,phone')->find($uid);
        if(!$member){
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        if(!db('member')->update([
            'id'=>$member['id'],
            'nickname'=>$sex,
            'dates'=>time()
        ])){
            return ['status'=>0,'msg'=>'用户性别修改失败'];
        }
        return ['status'=>1,'msg'=>'用户性别修改成功'];
    }

    /**
     * 修改头像
     * @param int $uid
     * @return array
     */
    public function upgrade_head($uid=0){
        if(!request()->isPost()){
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if(!$uid){
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        $member = db('member')->field('id,head,phone')->find($uid);
        if(!$member){
            return ['status'=>0,'msg'=>'用户不已存在'];
        }

        $uploadify = new Uploadify();
        $_result = $uploadify->upload_head();

        if(!db('member')->update([
            'id'=>$member['id'],
            'head'=>$_result['fullpath'],
            'dates'=>time()
        ])){
            return ['status'=>0,'msg'=>'用户头像修改失败'];
        }
        $fullpath = str_replace('//','/',$this->site['url'].$_result['fullpath']);
        return ['status'=>1,'msg'=>'用户性头像改成功','fullpath'=>$fullpath."?_id=".time()];
    }

    /**
     * 根据ip获取位置
     * @param string $ip
     * @param int $type
     * @return \think\response\Json
     */
	public function get_ip_location($ip='',$type=0){
        if(!$ip){
            return json([
                'status'=>0,
                'msg'=>'缺少必要参数IP地址'
            ]);
        }
        switch ($type){
            case 1:
                $location = http('http://restapi.amap.com/v3/ip',['key'=>config('AMAP.KEY'),'ip'=>$ip]);
                unset($location['status']);
                unset($location['info']);
                unset($location['infocode']);
                break;
            case 0:
            default:
                $location = $this->get_location_ip($ip);
                break;
        }
        return json([
            'status'=>1,
            'msg'=>'定位成功',
            'data'=>$location
        ]);

	}

    /**
     * 获取省份
     * @param string $limit
     * @return \think\response\Json
     */
	public function get_province($limit=''){
		$list = db('provinces')
            ->field('provinceid,province')
            ->where('type','eq',0)
            ->limit($limit)
            ->select();
		if(!$list){
		    return json([
                'status'=>0,
                'msg'=>'没有查到数据'
            ]);
        }

        return json([
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$list
        ]);
	}

    public function get_province_jsonp($limit=''){
        $list = db('provinces')
            ->field('provinceid,province')
            ->where('type','eq',0)
            ->limit($limit)
            ->select();
        if(!$list){
            return jsonp([
                'status'=>0,
                'msg'=>'没有查到数据'
            ]);
        }

        return jsonp([
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$list
        ]);
    }

    /**
     * 获取市区信息
     * @param string $provinceid
     * @param string $limit
     * @param string $q
     * @return \think\response\Json
     */
	public function get_city($provinceid='',$limit='',$q=''){
        $where=[];
	    if($provinceid){
            $where['provinceid']=$provinceid;
        }
        if($q){
            $where['city']=['like','%'.$q.'%'];
        }
        $list = db('cities')
            ->field('provinceid,cityid,city')
            ->where('type','eq',0)
            ->where($where)
            ->limit($limit)
            ->select();
        if(!$list){
            return json([
                'status'=>0,
                'msg'=>'没有查到数据'
            ]);
        }

        return json([
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$list
        ]);
    }

    /**
     * 获取县区/街道
     * @param string $cityid
     * @return \think\response\Json
     */
    public function get_areas($cityid=''){
	    if(!$cityid){
            return json([
                'status'=>0,
                'msg'=>'缺少必要的参数cityid'
            ]);
        }
        $list = db('areas')
            ->field('areaid,area')
            ->where('cityid','eq',$cityid)
            ->select();
        if(!$list){
            return json([
                'status'=>0,
                'msg'=>'没有查到数据'
            ]);
        }

        return json([
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$list
        ]);
    }

    /**
     * 检测验证码
     * @param string $verify
     * @param bool $clear
     * @return array
     */
    protected function check_verify($verify='',$clear=false){
        $d = cookie($verify.'_session_code');
        $f = cookie('?'.$verify.'_session_code');

        if(!$f){
            return ['status'=>0,'msg'=>'验证码已失效'];
        }
        if($verify!=$d){
            return ['status'=>0,'msg'=>'验证码不正确'];
        }
        if($clear){
            cookie($verify.'_session_code',null,time()-60*2);
        }
        return ['status'=>1,'msg'=>'验证码正确'];
    }

    /**
     * 获取密码
     * @param $pwd
     * @return bool|string
     */
    protected function get_password($pwd){
        return substr(md5($pwd),10,15);
    }
}