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
    private $path = ROOT_PATH . 'public' . DS;
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
            throw new Exception('no code');
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
        $access_token =$this->get_access_token(input('code'));
        $param=array(
            'access_token'=>$access_token['access_token'],
            'openid'=>$access_token['openid']
        );
        $_result = $this->http("$this->url/userinfo",$param);
        return json_decode($_result,true);
    }

    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("{$this->path}data/jsapi_ticket.json"));
       if(empty($data)){
           $accessToken = $this->getAccessToken();
           // 如果是企业号用以下 URL 获取 ticket
           // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
           $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
           $res = json_decode($this->httpGet($url));
           $ticket = $res->ticket;
           if ($ticket) {
               $fp = fopen("{$this->path}data/jsapi_ticket.json", "w");
               fwrite($fp, json_encode($res));
               fclose($fp);
           }
       }else{
           if ($data->expires_in < time()) {
               $accessToken = $this->getAccessToken();
               // 如果是企业号用以下 URL 获取 ticket
               // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
               $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
               $res = json_decode($this->httpGet($url));
               $ticket = $res->ticket;
               if ($ticket) {
                   $data->expires_in = time() + 7000;
                   $data->jsapi_ticket = $ticket;
                   $fp = fopen("{$this->path}data/jsapi_ticket.json", "w");
                   fwrite($fp, json_encode($data));
                   fclose($fp);
               }
           } else {
               $ticket = $data->jsapi_ticket;
           }
           return $ticket;
       }
    }

    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("{$this->path}data/access_token.json"));
        if(empty($data)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $fp = fopen("{$this->path}/data/access_token.json", "w");
                fwrite($fp, json_encode($res));
                fclose($fp);
            }
        }else{
            if ($data->expires_in < time()) {
                // 如果是企业号用以下URL获取access_token
                // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
                $res = json_decode($this->httpGet($url));
                $access_token = $res->access_token;
                if ($access_token) {
                    $data->expires_in = time() + 7000;
                    $data->access_token = $access_token;
                    $fp = fopen("{$this->path}/data/access_token.json", "w");
                    fwrite($fp, json_encode($data));
                    fclose($fp);
                }
            } else {
                $access_token = $data->access_token;
            }
        }
        return $access_token;
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

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}