<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  jswei30@gmail.com
# @Filename: Api.php  开发接口
# @Last modified by:   魏巍
# @Last modified time: 2017-11-22T16:23:55+08:00

namespace app\index\controller;

use getuisdk\IGtNotificationTemplate;
use getuisdk\IGtTransmissionTemplate;
use think\Validate;
use think\Session;
use getuisdk\IGeTui;
use getuisdk\IGtAppMessage;
use getuisdk\IGtSingleMessage;
use getuisdk\IGtTarget;

class Api extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
		
    }

	public function set_sysconf($uid=0,$notify=1,$power=0){
		if(!$uid){
			 return json(['status'=>0,'msg'=>'缺少参数']);
		}

		$p = db('sysconf')->where('uid','eq',$uid)->where('notify','eq',$notify)->find();
		
		if(!$p){
			if(!db('sysconf')->insert([
				'power'=>$power,
				'notify'=>$notify,
				'uid'=>$uid,
				'dates'=>time()
			])){
				return json(['status'=>0,'msg'=>'设置失败']);
			}
			return json(['status'=>1,'msg'=>'设置成功']);
		}else{
		
			if(!db('sysconf')->update([
				'id'=>$p['id'],
				'power'=>$power,
				'dates'=>time()
			])){
				return json(['status'=>0,'msg'=>'设置失败']);
			}
			return json(['status'=>1,'msg'=>'设置成功']);
		}
		
	}

    public function carousel($limit=5,$order="sort asc,date desc"){
        $_carousel = db('carousel')
            ->field('id,title,image,url,date')
            ->where('status','eq',0)
            ->limit($limit)
            ->order($order)
            ->select();
        if (!$_carousel) {
            return json(['status'=>0,'msg'=>'没有查到数据']);
        }
        foreach ($_carousel as $k => $v) {
            $_carousel[$k]['image']=$this->site['url'].$v['image'];
            $_carousel[$k]['date_format']=date('Y-m-d H:i:s',$v['date']);
        }
        return json(['status'=>1,'msg'=>'查询成功','data'=>$_carousel]);
    }

    /**
     * 个人信息
     * @param int $id
     * @return \think\response\Json
     */
    public function personal_info($id=0){
        if (!request()->isAjax()) {
            //return json(['status'=>0,'msg'=>'错误的请求方式']);
        }
        $_id = $id?$id:session('_mid');
        if (!$_id) {
            return json(['status'=>0,'msg'=>'缺少必要的条件']);
        }
        $userinfo = db('member')
            ->field('id as user_id,phone,birthday,password,push_client_id,address,information,nickname,head,email,last_login_time,last_login_ip,status')
            ->find($id);
        if (!$userinfo) {
            return json(['status'=>0,'msg'=>'查询失败']);
        }
        $userinfo['last_login_time_format']=$userinfo['last_login_time']?date('Y-m-d H:i:s',$userinfo['last_login_time']):'';
        $userinfo['birthday_format']=$userinfo['birthday']?date('Y-m-d',$userinfo['birthday']):'';
        $userinfo['head']=$userinfo['head']?$this->site['url'].$userinfo['head']:'';
        $userinfo['settings']=$this->get_setting($userinfo['user_id']);
        return json(['status'=>1,'msg'=>'查询成功','data'=>$userinfo]);
    }
	
	public function test($callback="CALLBACK"){
		$data=[
			'title'=>'小泽一郎',
			'msg'=>'浪打浪',
			'time'=>date('Y-m-d H:i:s',1521114139)
		];
		//echo $callback.'('.json_encode($data).')';
		return jsonp($data);
	}

    /**
     * 检昵称是否存在
     * @param string $nickname
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function check_nickname($nickname='')
    {
        if (empty($nickname)) {
            return json(['status'=>0,'msg'=>'请输入要修改的昵称']);
        }
        $member = db('member')->where(['nickname'=>$nickname])->find();
        if (empty($member)) {
            return json(['status'=>1,'msg'=>'恭喜您昵称未被注册可以使用']);
        } else {
            return json(['status'=>0,'msg'=>'抱歉昵称已被占用不可使用']);
        }
    }

    /**
     * 配置站点信息
     * @return array
     */
    public function get_site(){
        $site = db('config')
            ->field('title,short_title,address,copyright,icp,logo,keywords,description,url,radius,address,start_push_time,start_time,end_push_time,end_time,punch_ip,compny')
            ->find(1);
        $site['logo'] = $this->site['url'].$site['logo'];
        return ['status'=>1,'data'=>$site];
    }

    public function upgrade_address($uid=0,$address=''){
        if (!$uid) {
            return ['status'=>0,'msg'=>'参数错误'];
        }
        if (!$address) {
            return ['status'=>0,'msg'=>'地址不能为空'];
        }
        $member = db('member')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'没有用户'];
        }
        if(!db('member')->update([
            'id'=>$uid,
            'address'=>$address,
            'dates'=>time()
        ])){
            return ['status'=>0,'msg'=>'修改失败'];
        }
        return ['status'=>0,'msg'=>'修改成功'];
    }

    public function upgrade_birthday($uid=0,$date=''){
        if (!$uid) {
            return ['status'=>0,'msg'=>'参数错误'];
        }
        if (!$date) {
            return ['status'=>0,'msg'=>'地址不能为空'];
        }
        $member = db('member')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'没有用户'];
        }
        if(!db('member')->update([
            'id'=>$uid,
            'birthday'=>strtotime($date),
            'dates'=>time()
        ])){
            return ['status'=>0,'msg'=>'修改失败'];
        }
        return ['status'=>0,'msg'=>'修改成功'];
    }

    /**
     * 获取栏目
     * @param int $id
     * @return array
     */
    public function get_column($id=0)
    {
        $data = db('column')->field('id,title,name')
          ->where(['status'=>0,'fid'=>0])
          ->order('sort asc')->select();
        if ($id==0) {
            $data[0]['active']=1;
        } else {
            foreach ($data as $k => $v) {
                if ($v['id']==$id) {
                    $data[$k]['active']=1;
                } else {
                    $data[$k]['active']=0;
                }
            }
        }
        return ['status'=>1,'data'=>$data];
    }

    /**
     * @param string $phone
     * @param string $password
     * @param int $type
     * @param int $t
     * @param string $push_client_id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function login($phone='', $password='', $type=0,$t=1,$push_client_id='')
    {
        if (!request()->isPost()) {
            //return json(['status'=>0,'msg'=>'错误的请求方式']);
        }
        //逻辑判断
        if (empty($phone)) {
            return json(['status'=>0,'msg'=>'请输入您的账号']);
        }
        if (empty($password)) {
            return json(['status'=>0,'msg'=>'请输入您的密码']);
        }

        if (!$type) {     //账号登录
            $where=[
                'phone'=>$phone
            ];
            if (Validate::is($phone, 'email')) {
                $where=[
                    'email'=>$phone
                ];
            }
            $admin = db("member")
                ->field('id as user_id,phone,birthday,password,push_client_id,address,information,nickname,head,email,last_login_time,last_login_ip,status')
                ->where($where)->find();

            if (!$admin) {
                return json(['status'=>0,'msg'=>'账号有误或不存在']);
            }

            if ($admin['password']!=$this->get_password($password,$t)) {
                return json(['status'=>0,'msg'=>'您的密码输入有误']);
            }
        } else {
            $flag = $this->check_verify($password, true);  //验证码验证
            if (!$flag['status']) {
                return $flag;
            }
            $where=[
                'phone'=>$phone
            ];
            $admin = db("member")
                ->field('id as user_id,phone,password,nickname,information,head,sex,email,last_login_time,
                last_login_ip,status')
                ->where($where)->find();
            if (!$admin) {
                return json(['status'=>0,'msg'=>'您的账号输入有误']);
            }
        }

        if ($admin['status']==1) {
            return json(['status'=>0,'msg'=>'非法操作账号已锁定，请联系管理员解封']);
        }
        $data=array(
            'id'=>$admin['user_id'],
            'last_login_time'=>time(),
            'last_login_ip'=>request()->ip(),
            'last_login_address'=>$this->get_location()
        );
        $_cid = input('push_client_id/s');
        if($_cid){
            $data['push_client_id']=$_cid;
        }
        //p($data);die;
        //更新登陆信息
        if(db("member")->update($data)){
            $admin = db("member")
                ->field('id as user_id,phone,birthday,password,push_client_id,address,information,nickname,head,email,last_login_time,last_login_ip,status')
                ->where($where)->find();
        }
        //保存登录状态
        session('_mid', $admin['user_id']);
        session('_m', $admin['phone']);
        $admin['head']=$admin['head']?$this->site['url'].$admin['head']:'';
        $admin['last_login_time_format']=$admin['last_login_time']?date('Y-m-d H:i:s',$admin['last_login_time']):'';
        $admin['settings']=$this->get_setting($admin['user_id']);
        $admin['birthday_format']=$admin['birthday']?date('Y-m-d',$admin['birthday']):'';
        //跳转目标页
        unset($admin['password']);
        unset($admin['status']);
        return json(['status'=>1,'msg'=>'登录成功','data'=>$admin]);
    }

	public function get_setting($uid=0){
		if(!$uid){
			return[
			    'status'=>0,
                'msg'=>'缺少必要参数uid'
            ];
		}
		return db('sysconf')->field('notify,power')->where('uid','eq',$uid)->select();
	}

    /**
     * 用户注册
     * @param string $phone
     * @param string $password
     * @param string $push_client_id
     * @param string $verify
     * @return \think\response\Json|\think\response\View
     */
    public function register($phone='', $password='',$push_client_id='',$verify='',$t=1)
    {
        if (!request()->isPost()) {
            return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if (empty($verify)) {
            return json(['status'=>0,'msg'=>'请输入验证码']);
        }
        if (empty($phone)) {
            return json(['status'=>0,'msg'=>'请输入手机号']);
        }
        if (empty($password)) {
            return json(['status'=>0,'msg'=>'请输入您的密码']);
        }
        $flag = $this->check_verify($verify, true);  //验证码验证
        if (!$flag['status']) {
            return $flag;
        }
        $member = [
            'phone'=>$phone,
            'password'=>$this->get_password($password,$t),
            'tel'=>$phone,
            'push_client_id'=>$push_client_id,
            'date'=>time()
        ];
        $admin = db('member')->where('phone', 'eq', $phone)->find();

        if ($admin) {
            return json(['status'=>0,'msg'=>'用户已存在']);
        }
        if (!db('member')->insert($member)) {
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
    public function set_password($phone='', $password='', $confirm_password='',$t=1)
    {
        if (!request()->isPost()) {
            return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if (empty($phone)) {
            return json(['status'=>0,'msg'=>'参数错误',]);
        }
        if (empty($password)) {
            return json(['status'=>0,'msg'=>'请输入新密码']);
        }
        if (empty($confirm_password)) {
            return ['status'=>0,'msg'=>'请输入确认密码'];
        }
        if ($confirm_password!=$password) {
            return ['status'=>0,'msg'=>'两次密码不一致'];
        }
        $member = db('member')->field('id')
            ->field('date', true)
            ->where('phone', 'eq', $phone)
            ->find();
        if (empty($member)) {
            return ['status'=>0,'msg'=>'用户名不存在'];
        }
        if (!db('member')->update([
            'id'=>$member['id'],
            'password'=>$this->get_password($password,$t),
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'修改失败请重试'];
        }
        return ['status'=>1,'msg'=>'修改成功'];
    }

    /**
     * 用户退出
     * @return array
     */
    public function logout()
    {
        Session::delete('_mid');
        Session::delete('_m');
        return array('status'=>1,'msg'=>'退出成功');
    }

    /**
     * @author 魏巍
     * @description 发送验证码邮件
     * @param string   $email       邮箱
     * @return \think\response\json 返回发送结果
     */
    public function send_email_code($email='')
    {
        if (!request()->isPost()) {
            return json(['status'=>0,'msg'=>'请求方式错误']);
        }
        if (empty($email)) {
            return ['status'=>0,'msg'=>'请填写邮箱'];
        }
        if (!Validate::is($email, 'email')) {
            return ['status'=>0,'msg'=>'抱歉邮箱格式错误'];
        }
        $_code = NoRand(0, 9, 6);
        cookie($_code.'_session_code', $_code, 60*15);
        $html = "【".$this->site['title']."】:您本次的验证码:".
            $_code.",有效时间为15分钟.如果您没有使用【".$this->site['title']
            ."】相关产品,请自动忽略此邮件谢谢:)";

        if (!think_send_mail($email, $email, "【".$this->site['title']."】", $html)) {
            return ['status'=>0,'msg'=>'验证码发送失败,请稍后重试:('];
        }
        return ['status'=>1,'msg'=>'验证码发送成功到邮箱['.$email.']中,请及时查收:)'];
    }
    /**
     * 发送验证码
     * @param string $tel       手机号
     * @param int $type         类型:0通用,1注册,2重置密码
     * @return \think\response\json
     */
    public function send_message($tel='', $type=0)
    {
        if (!request()->isAjax()) {
            //return ['status'=>0,'msg'=>'请求方式错误'];
        }
        if (!$tel) {
            return ['status'=>0,'msg'=>'请输入发送手机号'];
        }
        if (!Validate::is($tel, '/^1[34578]\d{9}$/')) {
            return ['status'=>0,'msg'=>'请输入正确的手机号'];
        }

//        if ($type==0) {
//            $arr = send_sms($tel);
//        } elseif ($type==1) {
//            $arr = send_sms($tel);
//        } else {
//            $arr = send_sms($tel);
//        }
        $arr = send_sms($tel);
        if (substr($arr, 21, 6) == 000000) {
            return ['status'=>1,'msg'=>'验证成功下发,请注意查收'];
        } else if(substr($arr, 21, 6) == 105147) {
            return ['status'=>0,'msg'=>'接受手机使用太频繁'];
        }else{
			 return ['status'=>0,'msg'=>'验证下发失败'];
		}
    }

    /**
     *
     * @param int $uid
     * @param string $info
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function set_information($uid=0,$info=''){
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'请求方式错误'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        if (!$info) {
            return ['status'=>0,'msg'=>'缺少必要参数info'];
        }
        if(mb_strlen($info)>50){
            return ['status'=>0,'msg'=>'个性签名在50个字符之内'];
        }
        if(!db('member')->update([
            'id'=>$uid,
            'information'=>$info,
            'dates'=>time()
        ])){
            return ['status'=>0,'msg'=>'个性签名设置失败'];
        }
        return ['status'=>1,'msg'=>'个性签名设置成功'];
    }

    /**
     * [set_hobbise 设置个人喜好]
     * @param integer $id      [description]
     * @param string  $hobbies [description]
     */
    public function set_hobbise($id=0, $hobbies='')
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'请求方式错误'];
        }
        if (!$id) {
            return ['status'=>0,'msg'=>'缺少必要参数id'];
        }
        if (!$hobbies) {
            return ['status'=>0,'msg'=>'缺少必要参数hobbies'];
        }
        if (is_array($hobbies)) {
            $hobbies = implode(',', $hobbies);
        }
        $member = db('member')->field('id')->find($id);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        if (!db('member')->update([
            'id'=>$member['id'],
            'hobbise'=>$hobbies,
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'用户喜好设置失败'];
        }
        return ['status'=>1,'msg'=>'用户喜好设置成功'];
    }
    /**
     * 验证验证码
     * @param string $verify
     * @return array
     */
    public function check_code($verify='')
    {
        if (!$verify) {
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
    public function upgrade_nickname($uid=0, $nickname='')
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        if (!$nickname) {
            return ['status'=>0,'msg'=>'缺少必要参数nickname'];
        }
        $member = db('member')->field('id,nickname,phone')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        $count = db('member')
            ->where('nickname', 'like', '%'.$nickname.'%')
            ->count('*');
        if ($count) {
            return ['status'=>0,'msg'=>'用户昵称已存在'];
        }
        if (has_chiness($nickname) && mb_strlen($nickname)>30) {
            return ['status'=>0,'msg'=>'用户昵称过长'];
        }
        if (!has_chiness($nickname) && mb_strlen($nickname)>10) {
            return ['status'=>0,'msg'=>'用户昵称过长'];
        }
        if (!db('member')->update([
            'id'=>$member['id'],
            'nickname'=>$nickname,
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'用户昵称修改失败'];
        }
        return ['status'=>1,'msg'=>'用户昵称修改成功','nickname'=>$nickname];
    }

    public function upgrade_password($uid=0, $password='',$verify='',$t=1)
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        if(!$verify){
            return ['status'=>0,'msg'=>'请填写验证码'];
        }
        $flag = $this->check_verify($verify, true);  //验证码验证
        if (!$flag['status']) {
            return $flag;
        }
        $member = db('member')->field('id,nickname,phone')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        if (!db('member')->update([
            'id'=>$member['id'],
            'password'=>$this->get_password($password,$t),
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'密码修改错误'];
        }
        return ['status'=>1,'msg'=>'密码修改成功'];
    }

    /**
     * 修改性别
     * @param int $uid
     * @param int $sex
     * @return array
     */
    public function upgrade_sex($uid=0, $sex=0)
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        $member = db('member')->field('id,nickname,phone')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        if (!db('member')->update([
            'id'=>$member['id'],
            'nickname'=>$sex,
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'用户性别修改失败'];
        }
        return ['status'=>1,'msg'=>'用户性别修改成功'];
    }

    /**
     * 修改头像
     * @param int $uid
     * @return array
     */
    public function upgrade_head($uid=0)
    {
        
        if (!request()->isPost()) {
           return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        $member = db('member')->field('id,head,phone')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }

        $uploadify = new Uploadify();
        $_result = $uploadify->upload_head();
        $_path =  DS .'uploads' . DS .'head'.DS .$_result['filename'];
        if (!db('member')->update([
            'id'=>$member['id'],
            'head'=> $_path,
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'用户头像修改失败'];
        }
		if(is_file($member['head'])){
			unlink($member['head']);
		}

        $full_path = $this->site['url']. $_path;
        return ['status'=>1,'msg'=>'用户性头像改成功','fullpath'=>$full_path."?_id=".time()];
    }

    /**
     * 获取ip
     * @return array
     */
    public function get_ip(){
        $ip = get_real_client_ip();
        $_ip = get_client_ip();
        return ['status'=>1,'data'=>[
            'real_ip'=>$ip,
            'client_ip'=>$_ip
        ]];
    }

    /**
     * 更新手机
     * @param int $uid
     * @param string $phone
     * @param string $verify
     * @return array
     */
    public function upgrade_phone($uid=0, $phone='', $verify='')
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        if (!$phone) {
            return  ['status'=>0,'msg'=>'请输入手机号'];
        }
        if (!Validate::is($phone, '/^1[34578]\d{9}$/')) {
            return ['status'=>0,'msg'=>'手机号码不正确'];
        }
        if (!$verify) {
            return  ['status'=>0,'msg'=>'请输入验证码'];
        }
        $flag = $this->check_verify($verify, true);  //验证码验证
        if (!$flag['status']) {
            return $flag;
        }
        $member = db('member')->field('id,phone')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }
        if ($member['phone']==$phone) {
            return ['status'=>0,'msg'=>'手机号已存在,请更换一个'];
        }

        if (!db('member')->update([
            'id'=>$member['id'],
            'phone'=>$phone,
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'安全手机跟换失败'];
        }
        return ['status'=>1,'msg'=>'安全手机跟换成功'];
    }

    /**
     * 更换邮箱
     * @param int $uid
     * @param string $email
     * @param string $verify
     * @return array
     */
    public function upgrade_email($uid=0, $email='', $verify='')
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        if (!$email) {
            return ['status'=>0,'msg'=>'请输入邮箱'];
        }
        if (!Validate::is($email, 'email')) {
            return ['status'=>0,'msg'=>'邮箱格式不正确'];
        }
        if (!$verify) {
            return ['status'=>0,'msg'=>'请输入邮箱验证码'];
        }
        $flag = $this->check_verify($verify, true);  //验证码验证
        if (!$flag['status']) {
            return $flag;
        }
        $member = db('member')->field('id,email')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }

        if (!db('member')->update([
            'id'=>$member['id'],
            'email'=>$email,
            'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'安全邮箱跟换失败'];
        }
        return ['status'=>1,'msg'=>'安全邮箱跟换成功'];
    }

    /**
     * 根据ip获取位置
     * @param string $ip
     * @param int $type
     * @return \think\response\Json
     */
    public function get_ip_location($ip='', $type=0)
    {
        if (!$ip) {
            return json([
                'status'=>0,
                'msg'=>'缺少必要参数IP地址'
            ]);
        }
        switch ($type) {
            case 1:
                $param = ['key'=>config('AMAP.KEY'),'ip'=>$ip];
                $location = http('http://restapi.amap.com/v3/ip', $param);
                unset($location['status']);
                unset($location['info']);
                unset($location['infocode']);
                break;
            case 0:
            default:
                $_ip = new \service\IpLocation();
                $location = $_ip->get_location($ip);
                break;
        }
        return [
            'status'=>1,
            'msg'=>'定位成功',
            'data'=>$location
        ];
    }

    /**
     * 获取省份
     * @param string $limit
     * @return \think\response\Json
     */
    public function get_province($limit='')
    {
        $list = db('provinces')
            ->field('provinceid,province')
            ->where('type', 'eq', 0)
            ->limit($limit)
            ->select();
        if (!$list) {
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
     * 获取市区信息
     * @param string $provinceid
     * @param string $limit
     * @param string $q
     * @return \think\response\Json
     */
    public function get_city($provinceid='', $limit='', $q='')
    {
        $where=[];
        if ($provinceid) {
            $where['provinceid']=$provinceid;
        }
        if ($q) {
            $where['city']=['like','%'.$q.'%'];
        }
        $list = db('cities')
            ->field('provinceid,cityid,city')
            ->where('type', 'eq', 0)
            ->where($where)
            ->limit($limit)
            ->select();
        if (!$list) {
            return [
                'status'=>0,
                'msg'=>'没有查到数据'
            ];
        }

        return [
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$list
        ];
    }

    /**
     * 获取县区/街道
     * @param string $cityid
     * @return \think\response\Json
     */
    public function get_areas($cityid='')
    {
        if (!$cityid) {
            return json([
                'status'=>0,
                'msg'=>'缺少必要的参数cityid'
            ]);
        }
        $list = db('areas')
            ->field('areaid,area')
            ->where('cityid', 'eq', $cityid)
            ->select();
        if (!$list) {
            return [
                'status'=>0,
                'msg'=>'没有查到数据'
            ];
        }

        return [
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$list
        ];
    }
    /**
     * [push_app 推送消息APP]
     * @param  string $cid    [用户id]
     * @param  string $title  [消息头]
     * @param  string $text   [消息正文]
     * @param  string $banner [显示图片]
     * @param  string $url    [跳转地址]
     * @param  string $type    [类型]
     * @return [type]         [description]
     */
    public function push_app($cid='',$title='', $text='', $banner='https://www.baidu.com/img/bd_logo1.png',
    $url='',$type='notification')
    {
        $appId=config('GeTui.AppID');
        $appKey =config('GeTui.AppKey');
        $masterSecret =config('GeTui.MasterSecret');

        $igt = new IGeTui(null,$appKey,$masterSecret);
        $template = null;

        switch ($type){
            case 'notification':
                $template = new IGtNotificationTemplate();
                $template ->setAppId($appId);                      //应用appid
                $template ->setAppkey($appKey);                    //应用appkey
                //通知栏
                $template->setTransmissionType(1);
                $template ->setTitle($title);                       //通知栏标题
                $template ->setText($text);                         //通知栏内容
                $template->setIsRing(true);
                $template->setIsClearable(true);
                $template->setIsVibrate(true);
                $template->setLogoURL($banner);
                break;
            case 'link':
                if(!$url){
                    return [
                        'status'=>0,
                        'msg'=>'缺少必要参数URL'
                    ];
                }
                $template =  new \getuisdk\IGtLinkTemplate();
                $template ->setUrl($url); //打开连接地址
                $template ->setTitle($title);                       //通知栏标题
                $template ->setText($text);                         //通知栏内容
                $template->setIsRing(true);
                $template->setIsClearable(true);
                $template->setIsVibrate(true);
                $template->setLogoURL($banner);
                break;
            case 'download':
                if(!input('download')){
                    return [
                        'status'=>0,
                        'msg'=>'缺少必要参数DOWNLOAD'
                    ];
                }
                $_load = input('download');
                $download = strstr($_load,'&')?json_decode($_load,true):$_load;
                $template =  new \getuisdk\IGtNotyPopLoadTemplate();
                //通知栏
                $template ->setNotyTitle($title);                 //通知栏标题
                $template ->setNotyContent($text); //通知栏内容
                $template ->setNotyIcon("");                      //通知栏logo
                $template ->setIsBelled(true);                    //是否响铃
                $template ->setIsVibrationed(true);               //是否震动
                $template ->setIsCleared(true);                   //通知栏是否可清除
                //弹框
                $template ->setPopTitle($download['pop_title']);   //弹框标题
                $template ->setPopContent($download['pop_text']); //弹框内容
                $template ->setPopImage($download['pop_image']);           //弹框图片
                $template ->setPopButton1($download['pop_btn_left']);     //左键
                $template ->setPopButton2($download['pop_btn_right']);     //右键
                //下载
                $template ->setLoadIcon($download['load_icon']);           //弹框图片
                $template ->setLoadTitle($download['load_text']);
                $template ->setLoadUrl($download['load_url']);
                $template ->setIsAutoInstall(false);
                $template ->setIsActived(true);
                break;
            case 'transmission':
                $template = new \getuisdk\IGtTransmissionTemplate();
                $template->setAppId($appId);
                $template->setAppkey($appKey);
                $template->setTransmissionType(1);//透传消息类型
                $template->setTransmissionContent($text);//透传内容
                $apn = new \getuisdk\IGtAPNPayload();
                $alertmsg=new \getuisdk\DictionaryAlertMsg();
                $alertmsg->body="body";
                $alertmsg->actionLocKey="ActionLockey";
                $alertmsg->locKey="LocKey";
                $alertmsg->locArgs=array("locargs");
                $alertmsg->launchImage="launchimage";
                //iOS8.2 支持
                $alertmsg->title="Title";
                $alertmsg->titleLocKey="TitleLocKey";
                $alertmsg->titleLocArgs=array("TitleLocArg");
                $apn->alertMsg=$alertmsg;
                $apn->badge=1;
                $apn->sound="";
                $apn->addCustomMsg("payload","请填写内容");
                //$apn->contentAvailable=1;
                $apn->category="ACTIONABLE";
                $template->setApnInfo($apn);
                break;
        }

        $message = new IGtSingleMessage();
        $message->setIsOffline(true);//是否离线
        $message->setOfflineExpireTime(3600*12*1000);//离线时间
        $message->setData($template);//设置推送消息类型
        $message->setPushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送

        //接收方
        $target = new IGtTarget();
        $target->setAppId($appId);
        $target->setClientId($cid);

        $result = $igt->pushMessageToSingle($message,$target);
        return $result;
        if (!request()->isPost()) {
            //return ['status'=>0,'msg'=>'错误请求方式'];
        }
//        if (!$title) {
//            return [
//              'status'=>0,
//              'msg'=>'缺少参数[title]'
//          ];
//        }
//        if (!$text) {
//            return [
//              'status'=>0,
//              'msg'=>'缺少参数[text]'
//          ];
//        }

        /*
         //个推类
        $getui = new \service\GeTui();
        //通知样式
        $style = new \service\MessageStyle();
        $style->type=\service\MessageType::getui;
        $style->text=$text;
        $style->title=$title;
        $style->is_ring=true;
        $style->logourl=$banner?$banner:''; //http://q1.qlogo.cn/g?b=qq&nk=524314430&s=100&t=1511320210

        if ($url) {
            //链接消息
            $link = new \service\LinkNotifi();
            $link->cid=$cid;
            $link->set_style($style);
            $link->set_url($url);
            $_data  = $link->merge();
        } else {
            $link = new \service\TextNotifi();
            $link->cid=$cid;
            $link->set_style($style);
            $_data  = $link->merge();
        }
        $result = $getui->push_app($_data);
        $_url = $url?$url:'未填写';
        $_banner=$banner?$banner:'未填写';
        $data = [
          'title'=>$title,
          'content'=>$text,
          'url'=>$_url,
          'banner'=>$_banner,
          'date'=>time(),
          'type'=>0
        ];
        if ($result['result']=='ok') {
            $data['status']=1;
            db('message')->insert($data);
            return [
              'status'=>1,
              'msg'=>'消息推送成功'
            ];
        } else {
            $data['status']=0;
            db('message')->insert($data);
            return [
              'status'=>0,
              'msg'=>'消息推送失败'
            ];
        }
         */
    }
    /**
     * [push_recive 修改推送]
     * @param  integer $uid    [用户ID]
     * @param  integer $recive [推送：0不接受，1接受，默认：1]
     * @return [type]          [description]
     */
    public function push_recive($uid=0, $recive=1)
    {
        if (!request()->isPost()) {
            return ['status'=>0,'msg'=>'错误请求方式'];
        }
        if (!$uid) {
            return ['status'=>0,'msg'=>'缺少必要参数uid'];
        }
        $member = db('member')->field('id')->find($uid);
        if (!$member) {
            return ['status'=>0,'msg'=>'用户不已存在'];
        }

        if (!db('member')->update([
          'id'=>$member['id'],
          'is_recive'=>$recive,
          'dates'=>time()
        ])) {
            return ['status'=>0,'msg'=>'设置失败'];
        }
        return ['status'=>1,'msg'=>'设置成功'];
    }


    /**
     * 获取数据列表
     * @param string $mod
     * @param int $id
     * @return \think\response\Json
     */
    public function query($mod='article',$id=0,$p=1,$sql=0){
        $params = request()->param();
        $action = isset($params['action'])?$params['action']:'list';
        $field = isset($params['field'])?$params['field']:'*';
        $limit = isset($params['limit'])?$params['limit']:'';
        $order = isset($params['order'])?$params['order']:'';
        $random = isset($params['random'])?$params['random']:'';
        $_where = isset($params['where'])?json_decode($params['where'],true):'';
//        $sub = isset($params['long'])?$params['long']:50;
//        $suffix = isset($params['suffix'])?$params['suffix']:false;
        $column_id = isset($params['column_id'])?$params['column_id']:'';
        $join = isset($params['join'])?$params['join']:'';
        $content = isset($params['content'])?$params['content']:'';
        $where['status']=0;
		
        if($_where){
            foreach ($_where as $k => $v){
                if(isset($v['val'])){
                    if(empty($v['val'])){
                        continue;
                    }
                    if(trim($v['op'])=='between'){
                        $val = explode('and',trim($v['val']));
                        $where[$v['field']]=['between',[$val[0],$val[1]]];
                    }else if(trim($v['op'])=='like'){
                        $val = trim($v['val']);
                        $op=trim($v['op']);
                        $where[$v['field']]=[$op,"%{$val}%"];
                    }else{
                        $val = trim($v['val']);
                        $op=trim($v['op']);
                        $where[$v['field']]=[$op,$val];
                    }
                    
                }
            }
        }

        if($id){
            $where['id'] = $id;
        }
        if($column_id){
            $_column =  $this->_get_child($column_id);
            $where['column_id'] = ['in',$_column];
        }
    
        if(!empty($order)){
            $order =str_replace('_',' ',$order);
        }

        switch ($action){
            case 'page':    //分页数据
                //p($where);die;
                $list = db($mod)
                    ->where($where)
                    ->field($field)
                    ->order($order)
                    ->paginate($limit,false,[
                        'page'=>$p
                    ]);

                $list = json_decode(json_encode($list),true);
                foreach($list['data'] as $k=>$v){
                    $list['data'][$k]['date_format']=isset($v["date"])?date('Y-m-d',$v['date']):'';
                    $list['data'][$k]["image"]= isset($v["image"]) && $v["image"]?$this->site['url'] . DS . $v["image"]:'';
                    $list['data'][$k]["m_image"]=isset($v["image"]) && $v["image"]?$this->site['url'] . DS . get_m_image($v["image"]):'';
                    //$list['data'][$k]["description"]=isset($v["description"])?sub_str($v["description"],0,$sub,$suffix):'';
                }

				if($sql){
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
						'sql'=>db($mod)->getlastsql()
					]);
				}else{
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
					]);
				}
                break;
            case 'list':    //列表数据
                if($join){
                    $list = db($mod)
                        ->alias('a')
                        ->join($join['field'].' b',"b.".$join['key']."=a.id")
                        ->where($where)
                        ->field($field)
                        ->order($order)
                        ->limit($limit)
                        ->select();
                }else{
                    $list = db($mod)
                        ->where($where)
                        ->field($field)
                        ->order($order)
                        ->limit($limit)
                        ->select();
                }

                foreach($list as $k=>$v){
                    $list[$k]['date_format']=isset($v["date"])?date('Y-m-d H:i:s',$v['date']):'';
                    $list[$k]["image"]= isset($v["image"]) && $v["image"]?$this->site['url'] . DS . $v["image"]:'';
                    $list[$k]["m_image"]=isset($v["image"]) && $v["image"]?$this->site['url'] . DS . get_m_image($v["image"]):'';
                    //$list[$k]["description"]=isset($v["description"])?sub_str($v["description"],0,$sub,$suffix):'';
                    if(isset($v["pics"]) && !empty($v["pics"])){
                        $_pics = [];
                        $pics = explode(',',$v["pics"]);
                        foreach($pics as $k1=>$v1){
                            $_pics[]=$this->site['url']  . $v1;
                        }
                        $list[$k]["pics"] = $_pics;
                    }
                }

                if($sql){
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
						'sql'=>db($mod)->getlastsql()
					]);
				}else{
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
					]);
				}
                break;
            case 'details':     //详情数据
                if(!$where){
                    return json(['status'=>0,'msg'=>'缺少参数ID']);
                }
                $vo = db($mod)->field($field)->where($where)->find();
                if($mod=='article' || $mod=='comments' || $mod=='taste' || $mod=='line'){
                    db($mod)->where($where)->setInc('hits');
                }
                $vo['date_format']=isset($vo['date'])?date('Y-m-d',$vo['date']):'';
				$vo['date_format1']=isset($vo['date'])?date('Y-m-d H:i:s',$vo['date']):'';
                $vo['content']=isset($vo['content']) && $vo['content']?htmlspecialchars_decode($vo['content']):'';
                $vo["image"]=isset($vo["image"]) && $vo["image"]?$this->site['url'] . DS . $vo["image"]:'';
                //$vo["m_image"]=isset($vo["image"]) && $vo["image"]?$this->site['url'] . DS . get_m_image($vo["image"]):'';
                //$vo["description"]=sub_str($vo["description"],0,$sub,$suffix);
                if(isset($vo["pics"]) && !empty($vo["pics"])){
                    $_pics = [];
                    $pics = explode(',',$vo["pics"]);
                    foreach($pics as $k=>$v){
                        $_pics[$k]=$this->site['url']  . $v;
                    }
                    $vo["pics"] = $_pics;
                }

                if(isset($vo['image']) && strstr($vo['image'],'/uploads/uploadify/')){
                    $vo['image']= str_replace('/uploads/uploadify/',$this->site['url'].'/uploads/uploadify/',$vo['image']);
                }
                
                $vo['content']=str_replace('/uploads/KindEditor',$this->site['url'].'/uploads/KindEditor',$vo['content']);
                //$vo['content']=str_replace('<img src=','<img data-src=',$vo['content']);
                //$vo['content']=preg_replace('/src=/','data-src=',$vo['content']);
                return json([
                    'status'=>1,
                    'msg'=>'查询成功',
                    'data'=>$vo
                ]);
               break;
            case 'decode':  //文本反解析
                if(!$content){
                     return json([
                        'status'=>0,
                        'msg'=>'失败'
                    ]); 
                }
                $data =  htmlspecialchars_decode($content);
				if($sql){
					 return json([
						'status'=>1,
						'msg'=>'成功',
						'data'=>$data
					]);
				}else{
					 return json([
						'status'=>1,
						'msg'=>'成功',
						'data'=>$data,
					]);
				}
            break;
            case 'count':   //计数
                $vo = db($mod)->field($field)->where($where)->count('*');
				if($sql){
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'count'=>$vo,
						'sql'=>db($mod)->getlastsql()
					]);
				}else{
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'count'=>$vo,
					]);
				}
            break;
            case 'random':  //随机推荐
                $DB_PREFIX = config('database.prefix');
                $_random = $random?' and '.$random:'';
                $limit = $limit?$limit:5;
                $order = $order?$order:'id';
                $sql='SELECT '.$field.' FROM `'
                    .$DB_PREFIX.$mod.'` WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.$DB_PREFIX.$mod.'`))) '
                    .$_random.' ORDER BY '.$order.' LIMIT '.$limit;
                
                $list = db($mod)->query($sql);
                foreach($list as $k=>$v){
                    $list[$k]['date_format']=isset($v["date"])?date('Y-m-d',$v['date']):'';
                    $list[$k]["image"]= isset($v["image"])?$this->site['url'] . DS . $v["image"]:'';
                    $list[$k]["m_image"]=isset($v["image"])?$this->site['url'] . DS . get_m_image($v["image"]):'';
                }
                return json([
                    'status'=>1,
                    'msg'=>'查询成功',
                    'data'=>$list
                ]);
				if($sql){
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
						'sql'=>db($mod)->getlastsql()
					]);
				}else{
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
					]);
				}
                break;
            case 'correlation':     //分词搜索
                $keyword = isset($params['keyword'])?$params['keyword']:'';
                if(!$keyword){
                    return json(['status'=>0,'msg'=>'请输入相关词']);
                }

                $pscws = new \SCWS\PSCWS4('utf8');
                $pscws->set_dict(VENDOR_PATH.DS.'scws/pscws4/dict/dict.utf8.xdb');
                $pscws->set_rule(VENDOR_PATH.DS.'/scws/pscws4/etc/rules.ini');
                $pscws->send_text($keyword);
                $tops = $pscws->get_tops(10,'ns');
                
                $where1 = '';
                if($tops){
                    foreach ($tops as $k=>$v){
                        if($k<count($tops)-1){
                            $where1 .= 'or `title` like "%'.$v["word"].'%"';
                        }
                    }
                    $where1 .= 'or `title` like "%'.$keyword.'%"';
                }else{
                    $where1 .= 'or `title` like "%'.$keyword.'%"';
                }
               
                $where1 = substr($where1,2);
                $limit = $limit?$limit:config('paginate.list_rows');
                $list = db($mod)
                    ->field($field)
                    ->where($where)
                    ->where($where1)
                    ->limit($limit)
                    ->order($order)
                    ->select();

                foreach($list as $k=>$v){
                    $list[$k]['date_format']=isset($v["date"])?date('Y-m-d',$v['date']):'';
                    $list[$k]["image"]= isset($v["image"])?$this->site['url'] . DS . $v["image"]:'';
                    $list[$k]["m_image"]=isset($v["image"])?$this->site['url'] . DS . get_m_image($v["image"]):'';
                }

				if($sql){
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
						'sql'=>db($mod)->getlastsql()
					]);
				}else{
					 return json([
						'status'=>1,
						'msg'=>'查询成功',
						'data'=>$list,
					]);
				}
                break;
            case 'form':     //添加表单
                $form = isset($params['form'])?$params['form']:[];
                if(!$form){
                    return json([
                        'status'=>0,
                        'msg'=>'请填写表单'
                    ]);
                }
                $model = Model($mod);
                $result = $model->save($form);
                return json($result);
                break;
			case 'refresh':     //添加表单
                 $list = db($mod)
                    ->field($field)
                    ->where($where)
                    ->order($order)
                    ->select();
				 $count = db($mod)->where($where)->count('*');
                $limit = $limit?$limit:config('paginate.list_rows');
                foreach($list as $k=>$v){
                    $list[$k]['date_format']=isset($v["date"])?date('Y-m-d',$v['date']):'';
                    $list[$k]["image"]= (isset($v["image"]) && $v["image"])?$this->site['url'] . DS . $v["image"]:'';
                    $list[$k]["m_image"]=(isset($v["image"]) && $v["image"])?$this->site['url'] . DS . get_m_image($v["image"]):'';
                }
				 $pager = ceil($count/$limit);
				 return json([
					'status'=>1,
					'msg'=>'查询成功',
					'data'=>$list,
					'last_page'=>$pager
				]);
                break;
            default:
                return json([
                    'status'=>0,
                    'msg'=>'错误的请求'
                ]);
        }
    }

    public function applay($reason='',$from='',$to='',$type=0,$uid=0){
        if(!$reason){
            return [
                'status'=>0,
                'msg'=>'请填写请假事由'
            ];
        }
        if(!$from){
            return [
                'status'=>0,
                'msg'=>'请填选择假开始时间'
            ];
        }
        if(!$to){
            return [
                'status'=>0,
                'msg'=>'请填选择假结束时间'
            ];
        }
        if(!$type){
            return [
                'status'=>0,
                'msg'=>'请选择请假类型'
            ];
        }
        if(!$uid){
            return [
                'status'=>0,
                'msg'=>'缺少必要参数'
            ];
        }
        $data = request()->post();
        $data['title'] = '['.date('Y-m-d',time()).']请假条';
        $data['from'] = strtotime($from);
        $data['to']=strtotime($to);
        $data['date']=time();
        if(!db('applay')->insert($data)){
            return [
                'status'=>0,
                'msg'=>'操作失败,请重试'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'操作成功,等待审批'
        ];
    }

    /**
     * 打卡
     * @param int $uid
     * @return array
     */
    public function punch($uid=0){
        if(!$uid){
            return [
                'status'=>0,
                'msg'=>'缺少参数uid'
            ];
        }
        $m = model('punch');
        return $m->punch();
    }

    /**
     * 是否打卡
     * @param int $uid
     * @param int $t
     * @return mixed
     */
    public function is_punch($uid=0,$t=0){
        $m = model('punch');
        return $m->is_punch();
    }
    /**
     * 获取子栏目
     * @param int $id
     * @param bool $clear
     * @return array
     */
    protected function _get_child($id){
        $curr=db('column')->field('dates',true)->find($id);
        $column=db('column')->field('dates',true)->where('status','eq',0)->select();
        $data = \service\Category::getChildrenById($column,$curr['id']);
        $retVal = (!empty($data)) ? implode(',',$data).','.$id : $id;
        return $retVal;
    }


    /**
     * 检测验证码
     * @param string $verify
     * @param bool $clear
     * @return array
     */
    protected function check_verify($verify='', $clear=false)
    {
        $d = cookie($verify.'_session_code');
        $f = cookie('?'.$verify.'_session_code');

        if (!$f) {
            return ['status'=>0,'msg'=>'验证码已失效'];
        }
        if ($verify!=$d) {
            return ['status'=>0,'msg'=>'验证码不正确'];
        }else{
			if ($clear) {
				cookie($verify.'_session_code', null, time()-60*2);
			}
			return ['status'=>1,'msg'=>'验证码正确'];
		}
    }



    /**
     * 获取密码
     * @param $pwd
     * @return bool|string
     */
    protected function get_password($pwd,$t=0)
    {
        return $t?substr($pwd, 10, 15):substr(md5($pwd), 10, 15);
    }
}
