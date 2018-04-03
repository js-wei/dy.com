<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-11-21T13:34:15+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-22T15:29:13+08:00

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
        $this->AppID=$AppID?$AppID:Config('GeTui.AppID');
        $this->AppSecret=$AppSecret?$AppSecret:Config('GeTui.AppSecret');
        $this->AppKey=$AppKey?$AppKey:Config('GeTui.AppKey');
        $this->MasterSecret=$MasterSecret?$MasterSecret:Config('GeTui.MasterSecret');
        $this->ClientID=$ClientID?$ClientID:Config('GeTui.ClientID');
        $this->is_checked();
    }

    public function setClientID($ClientID)
    {
        $this->ClientID=$ClientID;
    }
    /**
     * [push_single 推送单个用户]
     * @param  [type] $data [推送数据]
     * @return [type]       [description]
     */
    public function push_single($data)
    {
        $access_token = $this->auth_token();
        $url = "{$this->url}/{$this->AppID}/push_single";
        $result = $this->http($url, $data, $access_token['auth_token']);
        return $result;
    }
    /**
     * [save_list_body 保存消息共同体]
     * @return [type] [description]
     */
    public function push_list($data)
    {
        $access_token = $this->auth_token();
        $data = json_decode($data, true);
        unset($data['cid']);
        unset($data['requestid']);
        $url = "{$this->url}/{$this->AppID}/save_list_body";
        $result = $this->http($url, $data, $access_token['auth_token']);
        if ($result['result']=='ok') {
            $_result = $this->save_list_body($result['taskid']);
            return $_result;
        } else {
            return $result;
        }
    }
    /**
     * [push_list 推送]
     * @param  string $taskid [description]
     * @param  string $cid    [description]
     * @return [type]         [description]
     */
    public function save_list_body($taskid='', $cid='')
    {
        if (!$taskid) {
            return ['status'=>0,'message'=>'缺少参数taskid'];
        }
        $data =[
          'cid'=>[$cid?$cid:$this->ClientID],
          'taskid'=>$taskid,
          'need_detail'=>true
        ];
        $data = json_encode($data, true);
        $access_token = $this->auth_token();
        $url = "{$this->url}/{$this->AppID}/push_list";
        $result = $this->http($url, $data, $access_token['auth_token']);
        return $result;
    }
    /**
     * [push_app 推送消息]
     * @param  string $data      [推送数据]
     * @param  array  $condition [推送条件]
     * @return [type]            [description]
     */
    public function push_app($data='', $condition=[])
    {
        $data = is_array($data)?$data:json_decode($data, true);
        unset($data['cid']);
        $data = $condition?array_merge($data, $condition):$data;
        $data = json_encode($data, true);
        $access_token = $this->auth_token();
        $url = "{$this->url}/{$this->AppID}/push_app";
        $result = $this->http($url, $data, $access_token['auth_token']);
        return $result;
    }
    /**
     * [stop_task 消息停止]
     * @param  string $taskid [description]
     * @return [type]         [description]
     */
    public function stop_task($taskid='')
    {
        if (!$taskid) {
            return ['status'=>0,'message'=>'缺少参数taskid'];
        }
        $access_token = $this->auth_token();
        $url = "{$this->url}/{$this->AppID}/stop_task/{$taskid}";
        $result = $this->http($url, $data, $access_token['auth_token']);
        return $result;
    }
    /**
     * [push_single_batch 批量发送单推消息]
     * @param  [type] $msg_list [description]
     * @return [type]           [description]
     */
    public function push_single_batch($list)
    {
        if (!$list) {
            return ['status'=>0,'message'=>'缺少参数msg_list'];
        }
        $data = [
          'msg_list'=>$list,
          'need_detail'=>true
        ];
        $data = json_encode($data, true);
        $access_token = $this->auth_token();
        $url = "{$this->url}/{$this->AppID}/push_single_batch";
        $result = $this->http($url, $data, $access_token['auth_token']);
        return $result;
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
        if (is_array($data)) {
            $data = json_encode($data);
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
class Notifi
{
    private $AppID;
    private $AppSecret;
    private $AppKey;
    public $is_offline=false;
    public $msgtype='notification';
    public $cid;
    public $requestid;
    public $duration_begin;
    public $duration_end;
    public $style;
    public $message;

    public function __construct($msgtype)
    {
        $this->AppID=Config('GeTui.AppID');
        $this->AppSecret=Config('GeTui.AppSecret');
        $this->AppKey=Config('GeTui.AppKey');
        $this->msgtype=$msgtype;
        $this->_init();
    }
    /**
     * [_init 初始化数据]
     * @return [type] [description]
     */
    private function _init()
    {
        $this->message=[
          'appkey'=>$this->AppID,
          'is_offline'=>$this->is_offline,
          'msgtype'=>$this->msgtype,
        ];
        $this->requestid=substr(md5(time()), 0, 16);
    }
    /**
     * [set_style 设置信息]
     * @param [MessageStyle] $style [信息]
     */
    public function set_style($style)
    {
        $this->style = $style;
    }
    /**
     * [filter_field 过滤参数]
     * @return [type] [description]
     */
    public function filter_field($tyle)
    {
        $_data=[];
        foreach ($tyle as $k => $v) {
            if ($v!==null) {
                $_data[$k]=$v;
            }
        }
        return $_data;
    }
}

class TextNotifi extends Notifi
{
    private $url;
    public $notification;
    public function __construct()
    {
        parent::__construct('notification');
        $this->msgtype='notification';
    }

    public function merge()
    {
        $this->is_valid();
        $this->notification=[
          'style'=>$this->style,
          'duration_begin'=>$this->duration_begin,
          'duration_end'=>$this->duration_end
        ];
        $data = json_decode(json_encode($this), true);
        unset($data['style']);
        unset($data['duration_begin']);
        unset($data['duration_end']);
        unset($data['is_offline']);
        unset($data['msgtype']);
        $data[$this->msgtype]['style']= $this->filter_field($data[$this->msgtype]['style']);
        return $data;
    }
    public function set_url($url)
    {
        $this->url=$url;
    }
    private function is_valid()
    {
        if (!$this->cid) {
            exit(json_encode([
            'status'=>0,
            'message'=>'缺少参数cid'
          ]));
        }
        if (!$this->style) {
            exit(json_encode([
              'status'=>0,
              'message'=>'缺少参数style'
            ]));
        }
    }
}

/**
 *
 */
class LinkNotifi extends Notifi
{
    private $url;
    public $link;
    public function __construct()
    {
        parent::__construct('link');
        $this->msgtype='link';
    }

    public function merge()
    {
        $this->is_valid();
        $this->link=[
          'style'=>$this->style,
          'url'=>$this->url,
          'duration_begin'=>$this->duration_begin,
          'duration_end'=>$this->duration_end
        ];
        $data = json_decode(json_encode($this), true);
        unset($data['style']);
        unset($data['duration_begin']);
        unset($data['duration_end']);
        unset($data['is_offline']);
        unset($data['msgtype']);
        $data[$this->msgtype]['style']= $this->filter_field($data[$this->msgtype]['style']);
        return $data;
    }
    public function set_url($url)
    {
        $this->url=$url;
    }
    private function is_valid()
    {
        if (!$this->cid) {
            exit(json_encode([
            'status'=>0,
            'message'=>'缺少参数cid'
          ]));
        }
        if (!$this->url) {
            exit(json_encode([
              'status'=>0,
              'message'=>'缺少参数url'
            ]));
        }
        if (!$this->style) {
            exit(json_encode([
              'status'=>0,
              'message'=>'缺少参数style'
            ]));
        }
    }
}


/**
 * [MessageStyle 消息样式]
 */
class MessageStyle
{
    public $type = MessageType::system;
    public $text;
    public $title;
    public $logo;
    public $logourl;
    public $banner_url;
    public $is_ring=false;
    public $is_vibrate=false;
    public $is_clearable=true;
    //展开通知
    public $big_style=MessageBigStyle::text;
    public $big_image_url;
    public $big_text;

    public function get_query_data()
    {
        $this->is_valid();
        $_result = $this->filter_field();
        return $_result;
    }

    /**
     * [is_valid 验证数据完整性]
     * @return boolean [description]
     */
    private function is_valid()
    {
        switch ($this->type) {
        case 4:
          if (!$this->banner_url) {
              exit(json_encode([
              'status'=>0,
              'message'=>'缺少通知内容banner_url'
            ]));
          }
          break;
        case 6:
          if (!$this->text) {
              exit(json_encode([
              'status'=>0,
              'message'=>'缺少通知内容text'
            ]));
          }
          if (!$this->title) {
              exit(json_encode([
              'status'=>0,
              'message'=>'缺少通知标题title'
            ]));
          }

          switch ($this->big_style) {
            case 2:
              if (!$this->big_text) {
                  exit(json_encode([
                    'status'=>0,
                    'message'=>'缺少参数长文本big_text'
                  ]));
              }
              break;
            case 3:
                if (!$this->big_image_url) {
                    exit(json_encode([
                      'status'=>0,
                      'message'=>'缺少参数大图的URL地址big_image_url'
                    ]));
                }
                if (!$this->banner_url) {
                    exit(json_encode([
                      'status'=>0,
                      'message'=>'缺少参数小图的URL地址banner_url'
                    ]));
                }
              break;
            case 1:
            default:
              if (!$this->big_image_url) {
                  exit(json_encode([
                    'status'=>0,
                    'message'=>'缺少参数大图的URL地址big_image_url'
                  ]));
              }
              break;
          }
          break;
        case 0:
        case 1:
        default:
          if (!$this->text) {
              exit(json_encode([
              'status'=>0,
              'message'=>'缺少通知内容text'
            ]));
          }
          if (!$this->text) {
              exit(json_encode([
              'status'=>0,
              'message'=>'缺少通知内容text'
            ]));
          }
          if (!$this->title) {
              exit(json_encode([
              'status'=>0,
              'message'=>'缺少通知内容title'
            ]));
          }
          break;
      }
    }
    /**
     * [filter_field 过滤参数]
     * @return [type] [description]
     */
    private function filter_field()
    {
        $_data=[];
        foreach ($this as $k => $v) {
            if ($v!==null) {
                $_data[$k]=$v;
            }
        }
        return $_data;
    }
}
/**
 * [MessageType 消息类型]
 */
class MessageType extends Enum
{
    const system = 0;
    const getui = 1;
    const picture = 4;
    const affluent = 6;
    const __default = self::system;
}
/**
 * [MessageBigStyle 展开消息类型]
 */
class MessageBigStyle extends Enum
{
    const image = 1;
    const text = 2;
    const picture = 4;
    const __default = self::text;
}
