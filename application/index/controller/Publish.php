<?php
namespace app\index\controller;
use app\admin\controller\Config;
use think\Validate;
use think\Session;
use Payment\Common\PayException;
use Payment\Client\Charge;
use think\Loader;


class Publish extends Base{
	
	protected function  _initialize(){
		parent::_initialize();
	}
    public function forget(){
        return view();
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
     * 产品
     * @param int $id
     * @param string $code
     * @return \think\response\View
     */
    public function detail($openid='',$id=0,$code=''){
        if(empty($id) || empty($code)){
            exit('关键参数错误');
        }
        if($openid){
            $code = base64_decode($code);     //个人代码
            $product = db('product')->field('dates',true)->find($id);
            //点击
            db('my_product')
                ->where('pid','eq',$id)
                ->where('mid','eq',$code-10000)
                ->setInc('click');


            //$tpl = is_weixin()?'wechat_default':'detail1';
            $this->assign('vo',$product);
            $this->assign('code',$code);
            $this->assign('openid',$openid);
            $this->assign('pid',$id);
            return view('detail1');
        }else{
            $url =  'http://pinkan.cn/wap/oder/get_openid?callback=t.jswei.top/product/detail&_id='.$id.'&_c='.$code;
            header('Location:'.$url);
        }
    }

    /**
     * @param string $name
     * @param string $tel
     * @param string $city
     * @param string $address
     * @param string $price
     * @param int $number
     * @param int $pid
     * @param int $code
     * @return \think\response\Json|\think\response\View
     */
    public function add_order($name='',$tel='',$city='',$address='',$price='',$number=1,$pid=0,$code=0,$t=1){
        if(empty($name)){
            return json(['status'=>0,'msg'=>'请输入您的姓名']);
        }
        if(empty($tel)){
            return json(['status'=>0,'msg'=>'请输入您的手机号']);
        }
        if(empty($city)){
            return json(['status'=>0,'msg'=>'请输入您所在城市']);
        }
        if(empty($address)){
            return json(['status'=>0,'msg'=>'请输入您的地址']);
        }
        if(empty($price)){
            return json(['status'=>0,'msg'=>'价格不能为空']);
        }
        if(empty($pid)){
            return json(['status'=>0,'msg'=>'产品id不能为空']);
        }
        if(empty($code)){
            return json(['status'=>0,'msg'=>'推广code不能为空']);
        }
        $openid = input('openid');
        if(!Validate::regex($tel,'/^1[34578]\d{9}$/')){
            return json(['status'=>0,'msg'=>'您的手机号不正确']);
        }
        if(empty($openid)){
            return json(['status'=>0,'msg'=>'缺少必要的条件']);
        }
        $product = db('product')->field('id,title,price')->find($pid);
        $product['total'] = $number * $product['price'];
        $product['address'] = $city .' '.$address;
        $product['name'] = $name;
        $product['tel'] = $tel;

        $data = [
            'mid'=> $code-10000,
            'proid'=>$pid,
            'ordid'=>build_order_no(),
            'ordtime'=>time(),
            'product'=>json_encode($product),
            'ordtitle'=>$product['title'],
            'ordbuynum'=>$number,
            'ordprice'=>$product['price'],
            'ordfee'=>$product['total'],
        ];
        if(!db('order')->insert($data)){
            return json([
               'status'=>0,
               'msg'=>'添加订单失败'
            ]);
        }

        $order = db('order')->where('ordid','eq',$data['ordid'])
            ->field('ordtitle,ordid,ordfee')->find();
        if(!$order){
            return json(['status'=>0,'msg'=>'错误的订单编号']);
        }
        $total_fee = $t?100:$order['ordfee'];
        //引入WxPayPubHelper
        vendor("WxPayPub.WxPayJsApiPay");
        $tools = new \JsApiPay();
        //file_put_contents('./data/openid.txt',$openid);
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['ordtitle']);
        $input->SetAttach(json_encode(['ordid'=>$order['ordid']]));
        $input->SetOut_trade_no($order['ordid']);
        $input->SetTotal_fee($total_fee);//$total_fee
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("");
        $input->SetNotify_url($this->site['url']."/notify/callback_wechat");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);                    //'onP74wOKIE0qSq54D1Qqr_0gypyY'
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        return ['status'=>1,'jsApiParameters'=>$jsApiParameters];




        //$this->assign('jsApiParameters',$jsApiParameters);
        //return view('wechat_pay');
         //return ['status'=>1,'tpl'=>$this->fetch('wechat_pay')];
//        $html_text =  $this->alipay1($data,1);
//        $content = '<iframe src="'.$html_text.'" name="iframepage" id="iframepage"  scrolling="no" frameborder="0"></iframe>';
//        $this->assign('content',$content);
//        return view('pay');
    }

    /**
     * 获取openid
     * @param array $order
     * @param int $t
     */
    public function openid($orderid='2017081155569850',$t=0){
        $openid = input('openid');
        if($openid){
            $orderid = input('orderid');
            $order = db('order')->where('ordid','eq',$orderid)
                     ->field('ordtitle,ordid,ordfee')->find();
            if(!$order){
                return json(['status'=>0,'msg'=>'错误的订单编号']);
            }
            $total_fee = $t?100:$order['ordfee'];
            //引入WxPayPubHelper
            vendor("WxPayPub.WxPayJsApiPay");
            $tools = new \JsApiPay();
            //file_put_contents('./data/openid.txt',$openid);
            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody($order['ordtitle']);
            $input->SetAttach(json_encode(['ordid'=>$order['ordid']]));
            $input->SetOut_trade_no($order['ordid']);
            $input->SetTotal_fee($total_fee);//$total_fee
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("");
            $input->SetNotify_url($this->site['url']."/notify/callback_wechat");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openid);                    //'onP74wOKIE0qSq54D1Qqr_0gypyY'
            $order = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            return view('wechat');
//            return json(['status'=>1,'jsApiParameters'=>$jsApiParameters]);
        }else{
            $url =  'http://pinkan.cn/wap/oder/get_openid?callback=t.jswei.top/index/publish/openid&orderid='.$orderid;
            header('Location:'.$url);
        }
    }

    /**
     * 生成支付
     * @param array $order
     * @param string $openid
     * @param int $t
     * @return mixed
     */
    public function wechat($order=[],$openid='',$t=0){
        $total_fee =$t?100:$order['ordfee'] * 100 ;          //
        $out_trade_no = $order['ordid'];
        if(!$openid){
            exit('缺少重要参数openid');
        }
        //引入WxPayPubHelper
        vendor("WxPayPub.WxPayJsApiPay");
        $tools = new \JsApiPay();
        file_put_contents('./data/openid.txt',$openid);
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['ordtitle']);
        $input->SetAttach(json_encode(['ordid'=>$order['ordid']]));
        $input->SetOut_trade_no("$out_trade_no");
        $input->SetTotal_fee("$total_fee");//$total_fee
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("");
        $input->SetNotify_url($this->site['url']."/notify/callback_wechat");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);                    //'onP74wOKIE0qSq54D1Qqr_0gypyY'
        $order = \WxPayApi::unifiedOrder($input);

        $jsApiParameters = $tools->GetJsApiParameters($order);
        //$this->assign('jsApiParameters',$jsApiParameters);
        //return view('wechat_pay');
        return $jsApiParameters;
    }

    /**
     * 支付宝支付
     * @param array $order      订单
     * @param int $t            类型:0正常支付,1测试支付
     * @return string
     */
    public function alipay1($order=[],$t=0){
        require_once(VENDOR_PATH.'alipay/alipay.config.php');
        Vendor('alipay.lib.alipay_submit','.class.php');
        /**************************请求参数**************************/
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $order['ordid'];
        //订单名称，必填
        $subject = $order['ordtitle'];
        //付款金额，必填
        $total_fee = $t?0.01:$order['ordfee'];  //$order['ordfee']
        //商品描述，可空
        $body = '';
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"       	=> "create_direct_pay_by_user",
            "partner"       	=> '2088021699723760',
            "seller_id"  		=> '2088021699723760',
            "payment_type"	=> "1",
            "qr_pay_mode"     => 4,
            'qrcode_width'   => '120',
            //"notify_url"		=> $this->site['url']."/notify/callback_alipay",
            "return_url"		=> $this->site['url']."/notify/callback_alipay",
            "out_trade_no"		=> $out_trade_no,
            "subject"			=> $subject,
            "total_fee"			=> $total_fee,
            "body"				=> "$body",
            "_input_charset"	=> trim(strtolower(strtolower('utf-8'))),
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            "extra_common_param"=> ''
        );
        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm1($parameter,"post", "提交");
        return $html_text;
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
        session('_m',$admin['phone']);

        //跳转目标页
        unset($admin['password']);
        unset($admin['id']);
        unset($admin['status']);
        return json(['status'=>1,'msg'=>'登录成功','data'=>$admin,'redirect'=>Url('/account/information')]);
    }
    /**
     * [logout 用户退出]
     * @return [type] [description]
     */
    public function logout(){
        Session::delete('_mid');
        Session::delete('_m');
        return array('status'=>1,'msg'=>'退出成功','redirect'=> Url('/'));
    }
    /**
     * @author 魏巍
     * @description 发送验证码邮件
     * @param string   $email       邮箱
     * @return 返回发送结果
     */
    public function send_email($email=''){
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

        cookie($_code.'_session_code',$_code,60*15);
        $html = "【".$this->site['title']."】:您本次的验证码:".$_code.",有效时间为15分钟.如果您没有使用【".$this->site['title']
            ."】相关产品,请自动忽略此邮件谢谢:)";


        if(!think_send_mail($email,$email,"【".$this->site['title']."】",$html)){
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