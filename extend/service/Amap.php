<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2017/11/10
 * Time: 10:33
 */
namespace service;
use service\Enum;

class Amap{
    private $key;
    public $msg;
    private $flag=true;
    private $url=[
        'table'=>'http://yuntuapi.amap.com/datamanage/table',
        'data'=>'http://yuntuapi.amap.com/datamanage/data',
        'around'=>'http://yuntuapi.amap.com/nearby/around',
        'local'=>'http://yuntuapi.amap.com/datasearch'
    ];

    public function __construct($key=''){
        if($key){
            $this->key =$key;
        }else{
            $this->key = config('AMAP.SECRET');
        }
    }

    /**
     * 创建数据表
     * @param $table
     * @return bool|mixed
     */
    public function create_table($table){
        return $this->http("{$this->url['table']}/create",$table);
    }

    /**
     *添加数据
     * @param $data
     * @return bool|mixed
     */
    public function insert($data){
        $data = $data->get_query_data();
        return $this->http("{$this->url['data']}/create",$data);
    }

    /**
     * 更新数据
     * @param $data
     * @return bool|mixed
     */
    public function update($data){
        return $this->http("{$this->url['data']}/update",$data);
    }

    /**
     * 删除数据
     * @param $data
     * @return bool|mixed
     */
    public function delete($data){
        $column = $data->get_data()->_append;
        $data = json_decode(json_encode($data),true);
        $data = array_merge($data,$column);
        unset($data['loctype']);
        return $this->http("{$this->url['data']}/delete",$data);
    }

    /**
     * @param Around $data
     * @return bool|mixed
     */
    public function around($data){
        $data = $data->get_array_data();
        return $this->http("{$this->url['around']}",$data,'get');
    }

    /**
     * 请求数据
     * @param string $api
     * @param $data
     * @param string $method
     * @return mixed
     */
    private function http($api='',$data,$method='post'){
        if(!$api){
            $this->flag = false;
            $this->msg=[
                'code'=>804,
                'massage'=>'错误的请求操作'
            ];
        }
        if(!$data){
            $this->flag = false;
            $this->msg=[
                'code'=>805,
                'massage'=>'没有请求的参数'
            ];
        }
        if(is_object($data)){
            $data = json_decode(json_encode($data),true);
        }

        //$data['sin']=$sig?$sig:'';
        $curl = new \Curl\Curl();
        $response = $curl->$method($api,$data);
        return json_decode($response->response,true);
    }
}

class Table{
    public $key;
    public $name;
    private $flag=true;
    private $msg;

    public function __construct($key=''){
        if($key){
            $this->key = $key;
        }else{
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 获取数据
     * @return mixed
     */
    public function get_query_data(){
        $this->is_check();
        if(!$this->flag){
            return $this->msg;
        }
        return json_decode(json_encode($this),true);
    }

    private function is_check(){
        if(!$this->key){
            $this->flag=false;
            $this->msg=[
              'code'=>801,
              'massage'=>'缺少参数key'
            ];
        }
        if(!$this->name){
            $this->flag=false;
            $this->msg=[
                'code'=>802,
                'massage'=>'缺少参数name'
            ];
        }
        if(strlen($this->name)>50){
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'参数name小于50个字符'
            ];
        }
    }
}

class Data{
    public $key;
    public $tableid;
    /**
     * @var LocType
     */
    public $loctype=LocType::lat2log;
    private $data;
    public function __construct($key=''){
        if($key){
            $this->key = $key;
        }else{
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 设置位置信息
     * @param  Poi $poi
     */
    public function set_data($poi){
        $this->data = $poi;
    }

    /**
     * 获取位置信息
     * @return Poi
     */
    public function get_data(){
        return $this->data;
    }

    /**
     * 获取数据
     * @return Array
     */
    public function get_query_data(){
        $data = json_decode(json_encode($this),true);
        $data['data']=json_encode($this->data->get_query_data());   //位置详细数据
        return $data;
    }

}

class Poi{
    /**
     * @var string
     */
    public $_name='';
    /**
     * @var string
     */
    public $_location='';
    /**
     * @var CoordType
     */
    public $coordtype=CoordType::gps;
    /**
     * @var string
     */
    public $_address='';
    /**
     * @var array
     */
    public $_append=[];

    private $flag=true;
    private $msg;

    public function get_query_data(){
        return $this->get_query_column();
    }

    /**
     * 获取自定义栏目内容
     * @return array
     */
    private function get_query_column(){
        $data = json_decode(json_encode($this),true);
        if($this->_append){
            foreach ($data['_append'] as $k=>$v){
                if($v){
                    if($k=='ids'){
                        return ['ids'=>$v];
                    }else{
                        $data[$k]=$v;
                    }
                }
            }
        }

        $this->is_check();
        if(!$this->flag){
            return $this->msg;
        }
        unset($data['_append']);
        return $data;
    }

    /**
     * 验证数据完整性
     */
    private function is_check(){
        if(!$this->_name){
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数name'
            ];
            return;
        }
        if(!$this->_location){
            $this->flag=false;
            $this->msg=[
                'code'=>802,
                'massage'=>'缺少参数location'
            ];
            return;
        }
        if(!$this->_address){
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'参数name小于50个字符'
            ];
            return;
        }
    }
}

class Serializable{
    public function get_array_data(){
        return json_decode(json_encode($this),true);
    }
    public function get_json_data(){
        return json_encode($this);
    }
}

class Local extends Serializable{
    public $key;
    public $tableid;
    public $keywords;
    public $city;
    public $filter;
    public $sortrule;
    public $limit=20;
    public $page=1;

    private $flag=false;
    private $msg;

    public function __construct($key=''){
        if($key){
            $this->key = $key;
        }else{
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 验证数据完整性
     */
    public function is_check(){
        if(!$this->key){
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数name'
            ];
            return;
        }
        if(!$this->tableid){
            $this->flag=false;
            $this->msg=[
                'code'=>802,
                'massage'=>'缺少参数tableid'
            ];
            return;
        }
        if(!$this->keywords){
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'缺少参数keywords'
            ];
            return;
        }
        if(!$this->city){
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'缺少参数city'
            ];
            return;
        }
    }
}


/**
 * 周边类
 * Class Around
 * @package service
 */
class Around extends Serializable{
    public $key;
    /**
     * @var
     */
    public $center;
    /**
     * @var int
     */
    public $radius=1000;
    /**
     * @var int
     */
    public $limit=30;
    /**
     * @var SearchType
     */
    public $searchtype=SearchType::line;
    /**
     * @var int
     */
    public $timerange=1800;

    private $flag=false;
    private $msg;

    public function __construct($key=''){
        if($key){
            $this->key = $key;
        }else{
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 验证时间完整性
     * @return bool|void
     */
    public function is_valid(){
        if(!$this->key){
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数key'
            ];
        }
        if(!$this->center){
            $this->flag=false;
            $this->msg=[
                'code'=>804,
                'massage'=>'缺少参数center'
            ];
        }
       return !$this->flag?$this->msg:true;
    }
}


class LocType extends Enum {
     const lat2log = 1;
     const address = 2;
     const __default = self::lat2log;
}

class CoordType extends Enum{
    const gps = 1;
    const autonavi = 2;
    const baidu=3;
    const __default = self::gps;
}

/**
 * 搜索radius(半径）值代表的类型
 * Class SearchType
 * @package service
 */
class SearchType extends  Enum{
    const line = 0;
    const drive = 1;
    const __default = self::drive;
}