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
		session('site',$this->site);
		$this->assign('site',$this->site );
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
}
