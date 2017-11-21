<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-11-21T13:34:15+08:00
# @Email:  524314430@qq.com
# @Last modified by:   jswei
# @Last modified time: 2017-11-21T17:06:07+08:00

namespace service;

/**
 *
 */
class GeTui
{
    private $AppID;
    private $AppSecret;
    private $AppKey;
    private $MasterSecret;
    private $ClientID;
    private $url='https://restapi.getui.com/v1';
    private $flag = false;
    private $msg = '';

    public function __construct($AppID='', $AppSecret='', $AppKey='', $MasterSecret='', $ClientID='')
    {
        $this->AppID=$AppID?$appID:Config('GeTui.AppID');
        $this->AppSecret=$AppSecret?$AppSecret:Config('GeTui.AppSecret');
        $this->AppKey=$AppKey?$AppKey:Config('GeTui.AppKey');
        $this->MasterSecret=$MasterSecret?$MasterSecret:Config('GeTui.MasterSecret');
        $this->is_checked();
    }

    public function setClientID($ClientID)
    {
        $this->ClientID=$ClientID;
    }

    public function push_single($title='', $msg='')
    {
        $access_token = $this->auth_token();
        $data =  [
          'message'=>[
            'appkey'=>$this->AppKey,
            'is_offline'=>true,
            'offline_expire_time'=>10000000,
            'msgtype'=>'notification'
          ],
          'notification'=>[
            "style"=>[
              "type"=> 6,
              "text"=> $msg,
              "title"=> $title,
              'big_style'=>2,
              'big_text'=>"[".$title."]".$msg
            ],
            'transmission_type'=>false,
            'transmission_content'=>'透传内容',
          ],
          'cid'=>$this->ClientID,
          'requestid'=>substr(md5(time()), 0, 16)
        ];
        $data = json_encode($data);
        $result = $this->http("{$this->url}/{$this->AppID}/push_single", $data, $access_token['auth_token']);
        p($result);
    }
    public function save_list_body()
    {
    }
    public function push_list()
    {
    }
    public function push_app()
    {
    }
    public function stop_task()
    {
    }
    public function push_single_batch()
    {
    }

    private function is_checked()
    {
        if (!$this->AppID) {
            exit(json_encode(['status'=>0,'msg'=>'缺少参数AppID']));
        }
        if (!$this->AppSecret) {
            exit(json_encode(['status'=>0,'msg'=>'缺少参数AppSecret']));
        }
        if (!$this->AppKey) {
            exit(json_encode(['status'=>0,'msg'=>'缺少参数AppKey']));
        }
        if (!$this->MasterSecret) {
            exit(json_encode(['status'=>0,'msg'=>'缺少参数MasterSecret']));
        }
    }
    /**
     * [auth_token 获取鉴权信息]
     * @return [type] [description]
     */
    protected function auth_token()
    {
        $path = './data/igetui.txt';
        $access_token = is_file($path)?file_get_contents($path):'';

        $timeStamp = $this->micro_time();
        $access_token = json_decode($access_token, true);

        if ($access_token && $access_token['expire_time']>$timeStamp) {
            return $access_token;
        } else {
            // 计算sign值
            $sign = hash('sha256', $this->AppKey . $timeStamp . $this->MasterSecret);
            $params["appkey"] = $this->AppKey;
            $params["timestamp"] = $timeStamp;
            $params["sign"] = $sign;
            $params = json_encode($params);
            $token = $this->http("{$this->url}/{$this->AppID}/auth_sign", $params);
            file_put_contents($path, json_encode($token));
            return $token;
        }
    }

    /**
     * 请求数据
     * @param string $api
     * @param $data
     * @param string $method
     * @return mixed
     */
    private function http($api='', $data, $access_token='', $method='post')
    {
        $curl = new \Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        if ($access_token) {
            $curl->setHeader('authtoken', $access_token);
        }
        $response = $curl->$method($api, $data);

        return json_decode($response->response, true);
    }

    private function micro_time()
    {
        list($usec, $sec) = explode(" ", microtime());
        $time = ($sec . substr($usec, 2, 3));
        return $time;
    }
}


/**
 *
 */
class Message
{
    private $AppID;
    private $AppSecret;
    private $AppKey;

    public function __construct()
    {
        $this->AppID=Config('GeTui.AppID');
        $this->AppSecret=Config('GeTui.AppSecret');
        $this->AppKey=Config('GeTui.AppKey');
        $this->is_checked();
    }
    public $message;
    public $notification;
}




/**
 * [Message 消息类型]
 */
class MessageType extends Enum
{
    const msg = 0;
    const __default = self::msg;
}
