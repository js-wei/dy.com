<?php
/**
 * Created by PhpStorm.
 * User: 魏巍
 * Date: 2016/7/5
 * Time: 14:31
 * @todo：微信开放平台授权登录
 */

namespace service;
use Think\Exception;

class Wechat{
    private $url="https://api.weixin.qq.com/sns";
    private $appid;
    private $secret;
    private $redirect_uri;

    /**
     * Wechat constructor. 构造函数
     * @param $appid        string 程序id
     * @param $secret       string 密匙
     * @param $redirect_uri string 跳转地址
     */
    public function __construct($appid,$secret,$redirect_uri){
        $this->appid=$appid;
        $this->secret=$secret;
        $this->redirect_uri=$redirect_uri;
    }

    /**
     *获取登录地址
     */
    public function get_open_url($state='',$scope='snsapi_login'){
        if(empty($this->appid)){
            throw new Exception('no appId');
        }
        if(empty($this->redirect_uri)){
            throw new Exception('no redirect_uri');
        }
        $url ="https://open.weixin.qq.com/connect/qrconnect?appid=$this->appid&redirect_uri=$this->redirect_uri&response_type=code&state={$state}&scope={$scope}";
        header("location:$url");
        exit();
    }
    /**
     * 获取access_token
     * @param $code
     * @return mixed
     * @throws \Exception
     */
    public function get_access_token($code){
        if(empty($code)){
            //throw new Exception('no code');
        }
        $param=array(
            'appid'=>$this->appid,
            'secret'=>$this->secret,
            'code'=>$code,
            'grant_type'=>'authorization_code'
        );
        $_result = $this->http("$this->url/oauth2/access_token",$param);
        return json_decode($_result,true);
    }

    /**
     * 获取个人信息
     * @return mixed
     * @throws \Exception
     */
    public function get_userinfo(){
        $access_token =$this->get_access_token(I('get.code'));
        $param=array(
            'access_token'=>$access_token['access_token'],
            'openid'=>$access_token['openid']
        );
        $_result = $this->http("$this->url/userinfo",$param);
        return json_decode($_result,true);
    }

    /**
     * 请求数据
     * @param $url      string  地址
     * @param $param    array   参数
     * @return bool
     */
    private function http($url,$param){
        if($param != null) {
            $query = http_build_query($param);
            $url = $url . '?' . $query;
        }
        $ch = curl_init();
        if(stripos($url, "https://") !== false){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        if(intval($status["http_code"]) == 200) {
            return $content;
        }else{
            echo $status["http_code"];
            return false;
        }
    }
}