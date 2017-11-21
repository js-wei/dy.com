<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:40:01+08:00


namespace service;

class Amap
{
    private $key;
    public $msg;
    private $flag=true;
    private $url=[
        'table'=>'http://yuntuapi.amap.com/datamanage/table',
        'data'=>'http://yuntuapi.amap.com/datamanage/data',
        'around'=>'http://yuntuapi.amap.com/nearby/around',
        'datasearch'=>'http://yuntuapi.amap.com/datasearch'
    ];

    public function __construct($key='')
    {
        if ($key) {
            $this->key =$key;
        } else {
            $this->key = config('AMAP.SECRET');
        }
    }

    /**
     * 创建数据表
     * @param $table
     * @return bool|mixed
     */
    public function create_table($table)
    {
        return $this->http("{$this->url['table']}/create", $table);
    }

    /**
     *添加数据
     * @param $data
     * @return bool|mixed
     */
    public function insert($data)
    {
        $data = $data->get_query_data();
        return $this->http("{$this->url['data']}/create", $data);
    }

    /**
     * 更新数据
     * @param $data
     * @return bool|mixed
     */
    public function update($data)
    {
        return $this->http("{$this->url['data']}/update", $data);
    }

    /**
     * 删除数据
     * @param $data
     * @return bool|mixed
     */
    public function delete($data)
    {
        $column = $data->get_data()->_append;
        $data = json_decode(json_encode($data), true);
        $data = array_merge($data, $column);
        unset($data['loctype']);
        return $this->http("{$this->url['data']}/delete", $data);
    }

    /**
     * @param Around $data
     * @return bool|mixed
     */
<<<<<<< HEAD
    public function around($data){
        $_data = $data->get_query_data();
        return $this->http("{$this->url['datasearch']}/around",$_data,'get');
=======
    public function around($data)
    {
        $data = $data->get_array_data();
        return $this->http("{$this->url['around']}", $data, 'get');
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
    }

    /**
     * 获取本地位置信息
     * @param Local $local
     * @return mixed
     */
    public function get_location_pois($local){
        $data = $local->get_array_data();
        return $this->http("{$this->url['datasearch']}/local",$data,'get');
    }

    /**
     * 查询详情
     * @param DetailPoi $poi
     * @return mixed
     */
    public function detail_poi($poi){
        $data = $poi->get_array_data();
        return $this->http("{$this->url['datasearch']}/id",$data,'get');
    }
    /**
     * 请求数据
     * @param string $api
     * @param $data
     * @param string $method
     * @return mixed
     */
    private function http($api='', $data, $method='post')
    {
        if (!$api) {
            $this->flag = false;
            $this->msg=[
                'code'=>804,
                'massage'=>'错误的请求操作'
            ];
        }
        if (!$data) {
            $this->flag = false;
            $this->msg=[
                'code'=>805,
                'massage'=>'没有请求的参数'
            ];
        }
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
        }
<<<<<<< HEAD
        //$data['sin']=$sig?$sig:'';
=======
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
        $curl = new \Curl\Curl();
        $response = $curl->$method($api, $data);
        return json_decode($response->response, true);
    }
}

<<<<<<< HEAD
/**
 * 数据表
 * Class Table
 * @package service
 */
class Table{
=======
class Table
{
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
    public $key;
    public $name;
    private $flag=true;
    private $msg;

    public function __construct($key='')
    {
        if ($key) {
            $this->key = $key;
        } else {
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 获取数据
     * @return mixed
     */
    public function get_query_data()
    {
        $this->is_check();
        if (!$this->flag) {
            return $this->msg;
        }
        return json_decode(json_encode($this), true);
    }

    private function is_check()
    {
        if (!$this->key) {
            $this->flag=false;
            $this->msg=[
              'code'=>801,
              'massage'=>'缺少参数key'
            ];
        }
        if (!$this->name) {
            $this->flag=false;
            $this->msg=[
                'code'=>802,
                'massage'=>'缺少参数name'
            ];
        }
        if (strlen($this->name)>50) {
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'参数name小于50个字符'
            ];
        }
    }
}

<<<<<<< HEAD
/**
 * 插入数据
 * Class Data
 * @package service
 */
class Data{
=======
class Data
{
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
    public $key;
    public $tableid;
    /**
     * @var LocType
     */
    public $loctype=LocType::lat2log;
    private $data;
    public function __construct($key='')
    {
        if ($key) {
            $this->key = $key;
        } else {
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 设置位置信息
     * @param  Poi $poi
     */
    public function set_data($poi)
    {
        $this->data = $poi;
    }

    /**
     * 获取位置信息
     * @return Poi
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     * 获取数据
     * @return Array
     */
    public function get_query_data()
    {
        $data = json_decode(json_encode($this), true);
        $data['data']=json_encode($this->data->get_query_data());   //位置详细数据
        return $data;
    }
}

<<<<<<< HEAD
/**
 * 位置信息表
 * Class Poi
 * @package service
 */
class Poi{
=======
class Poi
{
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
    /**
     * @var int 主键
     */
    public $_id=0;
    public $tableid;
    public $key;
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

    public function get_query_data()
    {
        return $this->get_query_column();
    }

    /**
     * 获取自定义栏目内容
     * @return array
     */
    private function get_query_column()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->_append) {
            foreach ($data['_append'] as $k=>$v) {
                if ($v) {
                    if ($k=='ids') {
                        return ['ids'=>$v];
                    } else {
                        $data[$k]=$v;
                    }
                }
            }
        }

        $this->is_check();
        if (!$this->flag) {
            return $this->msg;
        }
        unset($data['_append']);
        return $data;
    }

    /**
     * 验证数据完整性
     */
    private function is_check()
    {
        if (!$this->_name) {
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数name'
            ];
            return;
        }
        if (!$this->_location) {
            $this->flag=false;
            $this->msg=[
                'code'=>802,
                'massage'=>'缺少参数location'
            ];
            return;
        }
        if (!$this->_address) {
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'参数name小于50个字符'
            ];
            return;
        }
    }
}

<<<<<<< HEAD
/**
 * 查询POi详情
 * Class DetailPoi
 * @package service
 */
class DetailPoi extends Serializable {
    /**
     * @var int 主键
     */
    public $_id=0;
    public $tableid;
    public $key;

    private $flag=true;
    private $msg;

    public function __construct($key=''){
        if($key){
            $this->key = $key;
        }else{
            $this->key = config('AMAP.KEY');
        }
    }

    public function get_query_data(){
        $data = json_decode(json_encode($this),true);
        $this->is_check();
        if(!$this->flag){
            return $this->msg;
        }
        return $data;
    }
    /**
     * 验证数据完整性
     */
    private function is_check(){
        if(!$this->key){
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数key'
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
        if(!$this->_id){
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'缺少参数id'
            ];
            return;
        }
    }
}

/**
 * 本地搜索类
 * Class Local
 * @package service
 */
class Local extends Serializable{
=======
class Serializable
{
    public function get_array_data()
    {
        return json_decode(json_encode($this), true);
    }
    public function get_json_data()
    {
        return json_encode($this);
    }
}

class Local extends Serializable
{
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
    public $key;
    public $tableid;
    public $limit=20;
    public $page=1;

    public $keywords=' ';
    public $city = '全国';
    public $filter;
    public $sortrule='_weight:0';

    private $flag=false;
    private $msg;

    public function __construct($key='')
    {
        if ($key) {
            $this->key = $key;
        } else {
            $this->key = config('AMAP.KEY');
        }
    }

    /**
     * 获取请求数据
     * @return array
     */
    public function get_query_data(){
        $this->is_check();
        if(!$this->flag){
            exit(json_encode($this->msg));
        }
        $data = $this->get_array_data();
        ksort($data);
        return $data;
    }

    /**
     * 设置过滤参数
     * @param array $filter
     */
    public function set_filters($filter=[]){
        $_filter='';
        if($filter){
            foreach ($filter as $k=>$v){
                $_filter.= "+".$k.'='.$v;
            }
            $_filter = substr($_filter,1);
        }
        $this->filter = $_filter;
    }

    /**
     * 验证数据完整性
     */
<<<<<<< HEAD
    private function is_check(){
        if(!$this->key){
=======
    public function is_check()
    {
        if (!$this->key) {
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数name'
            ];
            return;
        }
        if (!$this->tableid) {
            $this->flag=false;
            $this->msg=[
                'code'=>802,
                'massage'=>'缺少参数tableid'
            ];
            return;
        }
        if (!$this->keywords) {
            $this->flag=false;
            $this->msg=[
                'code'=>803,
                'massage'=>'缺少参数keywords'
            ];
            return;
        }
        if (!$this->city) {
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
class Around extends Serializable
{
    public $key;
    public $tableid;
    public $keywords=' ';
    /**
     * @var string
     */
    public $center;
    /**
     * @var int
     */
    public $radius=3000;
    /**
     * @var double
     */
    public $tableid=0;
    /**
     * @var int
     */
    public $limit=30;
    /**
     * @var array
     */
    public $filter;
    /**
     * @var string
     */
    public $sortrule='_ weight:0';
    /**
     * @var int
     */
    public $page=1;

    private $flag=true;
    private $msg;

    public function __construct($key='')
    {
        if ($key) {
            $this->key = $key;
        } else {
            $this->key = config('AMAP.KEY');
        }
    }

    /**
<<<<<<< HEAD
     * 设置过滤参数
     * @param array $filter
     */
    public function set_filters($filter=[]){
        $_filter='';
        if($filter){
            foreach ($filter as $k=>$v){
                $_filter.= "+".$k.'='.$v;
            }
            $_filter = substr($_filter,1);
        }
        $this->filter = $_filter;
    }

    public function get_query_data(){
        $this->is_valid();
        if(!$this->flag){
          exit(json_encode($this->msg));
        }else{
            $data = $this->get_array_data();
            ksort($data);
            return $data;
        }
    }

    /**
     * 验证时间完整性
     * @return bool|void
     */
    private function is_valid(){
        if(!$this->key){
=======
     * 验证完整性
     * @return bool|void
     */
    public function is_valid()
    {
        if (!$this->key) {
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
            $this->flag=false;
            $this->msg=[
                'code'=>801,
                'massage'=>'缺少参数key'
            ];
        }
<<<<<<< HEAD
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
        if(!$this->center){
=======
        if (!$this->center) {
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
            $this->flag=false;
            $this->msg=[
                'code'=>804,
                'massage'=>'缺少参数center'
            ];
        }
<<<<<<< HEAD

    }
}


/**
 * Class Serializable
 * @package service
 */
class Serializable{
    public function get_array_data(){
        return json_decode(json_encode($this),true);
    }
    public function get_json_data(){
        return json_encode($this);
=======
        return !$this->flag?$this->msg:true;
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
    }
}


<<<<<<< HEAD
/**
 * Class LocType
 * @package service
 */
class LocType extends Enum {
     const lat2log = 1;
     const address = 2;
     const __default = self::lat2log;
}

/**
 *
 * Class CoordType
 * @package service
 */
class CoordType extends Enum{
=======
class LocType extends Enum
{
    const lat2log = 1;
    const address = 2;
    const __default = self::lat2log;
}

class CoordType extends Enum
{
>>>>>>> 19036e92e85ade35ad278ccb99e0880219a90a40
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
class SearchType extends Enum
{
    const line = 0;
    const drive = 1;
    const __default = self::drive;
}
