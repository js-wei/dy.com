<?php
namespace app\index\controller;
use think\Controller;
use think\Session;

class Base extends Controller{
	
    protected function _initialize(){
    	header('Content-type:text/html;charset=utf-8;');
		set_time_limit(0);
		//常用变量
		$this->action = request()->action();
		$this->controller = request()->controller();
		$this->module = request()->module();
		$this->assign('action',strtolower($this->action));
		$this->assign('controller',strtolower($this->controller));
		$this->assign('module',strtolower($this->module));
		$this->site = db('Config')->order('id asc')->find();
		$this->get_massage_new_count();
		session('site',$this->site);
		$this->assign('site',$this->site );

    }

    protected  function get_massage_new_count(){
        $id = session('_mid');
        $msg_count =  db('message')
                ->where('mid','eq',$id)
                ->where('status','eq',0)
                ->count();
        $msg =  db('message')
            ->where('mid','eq',$id)
            ->where('status','eq',0)
            ->limit(4)
            ->select();
        $this->assign('_msg_count',$msg_count);
        $this->assign('_msg',$msg);
    }

    public function get_location($ip=''){
        $_result='';
        $ip = $ip?$ip:get_client_ip();
        $curl = new \Curl\Curl();
        $curl->post('http://freeapi.ipip.net', array(
            'ip' => $ip
        ));
        if ($curl->error) {
            return false;
        }
        else {
            $temp = explode('",',$curl->response);
            $temp[1]=str_replace('"','',$temp[1]);
            $temp[2] = str_replace('"','',$temp[2]);
            $_result = $temp[2]?$temp[2]:$temp[1];
        }
        return $_result;
    }
    /**
     * 获取短路由
     * @param string $source
     * @param string $url_long
     */
    public function get_short_url($url_long='',$source='487035677'){
        $curl = new \Curl\Curl();
        $curl->post('http://api.t.sina.com.cn/short_url/shorten.json', array(
            'source' => $source,
            'url_long' =>$url_long
        ));
        if ($curl->error) {
            return $curl->error_code;
        }
        else {
            return $curl->response;
        }
    }
}
